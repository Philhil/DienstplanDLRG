<?php

namespace App\Http\Controllers;

use App\Position;
use App\PositionCandidature;
use App\Training_user;
use App\User;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\Enum\EventStatus;
use Eluceo\iCal\Domain\ValueObject\Date;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\SingleDay;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;

class ICalController extends Controller
{
    public function feed(string $token)
    {
        $user = User::where('ical_token', $token)->firstOrFail();

        $includeServices  = request('services', 'true') === 'true';
        $includeTrainings = request('trainings', 'true') === 'true';

        $events = [];

        if ($includeServices) {
            $servicePositions = Position::with(['service', 'qualification'])
                ->where('user_id', $user->id)
                ->whereNotNull('service_id')
                ->whereHas('service')
                ->get();

            foreach ($servicePositions as $position) {
                $service = $position->service;
                $prefix = 'Dienst' . ($position->qualification ? ' (' . $position->qualification->name . ')' : '');
                $title = $this->serviceTitle($prefix, $service->date, $service->dateEnd ?? null, $service->comment ?? null);
                $event = $this->buildEvent($title, $service->date, $service->dateEnd ?? null, $service->location ?? null, $service->comment ?? null);
                $event->setStatus(EventStatus::CONFIRMED());
                $events[] = $event;
            }

            $candidatures = PositionCandidature::with(['position.service', 'position.qualification'])
                ->where('user_id', $user->id)
                ->whereHas('position.service')
                ->get();

            $confirmedServiceIds = $servicePositions->pluck('service_id')->filter()->unique();

            foreach ($candidatures as $candidature) {
                $position = $candidature->position;
                $service = $position->service;

                if ($confirmedServiceIds->contains($service->id)) {
                    continue;
                }

                $prefix = 'Dienst (nicht bestätigt)' . ($position->qualification ? ' (' . $position->qualification->name . ')' : '');
                $title = $this->serviceTitle($prefix, $service->date, $service->dateEnd ?? null, $service->comment ?? null);
                $event = $this->buildEvent($title, $service->date, $service->dateEnd ?? null, $service->location ?? null, $service->comment ?? null);
                $event->setStatus(EventStatus::TENTATIVE());
                $events[] = $event;
            }
        }

        if ($includeTrainings) {
            $trainingUsers = Training_user::with(['training', 'position.qualification'])
                ->where('user_id', $user->id)
                ->whereHas('training')
                ->get();

            foreach ($trainingUsers as $trainingUser) {
                $training = $trainingUser->training;
                $qualification = $trainingUser->position->qualification ?? null;
                $prefix = ($training->title ?? 'Übung') . ($qualification ? ' (' . $qualification->name . ')' : '');
                $title = $this->serviceTitle($prefix, $training->date, $training->dateEnd ?? null, $training->content ?? null);
                $event = $this->buildEvent($title, $training->date, $training->dateEnd ?? null, $training->location ?? null, $training->content ?? null);
                $event->setStatus(EventStatus::CONFIRMED());
                $events[] = $event;
            }
        }

        $calendar = new Calendar($events);
        $iCalComponent = (new CalendarFactory())->createCalendar($calendar);

        return response((string) $iCalComponent, 200)
            ->header('Content-Type', 'text/calendar; charset=UTF-8')
            ->header('Content-Disposition', 'inline; filename="dienstplan.ics"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function serviceTitle(string $prefix, $date, $dateEnd, ?string $description): string
    {
        $title = $prefix . ': ' . $date->format('H:i');
        if (!empty($dateEnd)) {
            $title .= ' – ' . $dateEnd->format('H:i');
        }
        if (!empty($description)) {
            $title .= ' ' . $description;
        }
        return $title;
    }

    private function buildEvent(string $summary, $date, $dateEnd, ?string $location, ?string $description): Event
    {
        if (!empty($dateEnd)) {
            $start = new DateTime(new \DateTime($date->toDateTimeString()), true);
            $end = new DateTime(new \DateTime($dateEnd->toDateTimeString()), true);
            $occurrence = new TimeSpan($start, $end);
        } else {
            $occurrence = new SingleDay(new Date(new \DateTimeImmutable($date->toDateString())));
        }

        $event = new Event();
        $event->setOccurrence($occurrence);
        $event->setSummary($summary);

        if (!empty($location)) {
            $event->setLocation(new Location($location, $location));
        }

        if (!empty($description)) {
            $event->setDescription($description);
        }

        return $event;
    }
}
