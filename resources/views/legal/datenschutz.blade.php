@extends('_layouts.base')

@section('body')
    <body>
        <div class="card">
            <div class="body">
                <h1>Datenschutzerklärung</h1>

                <h2>Einleitung</h2>

                <p>Mit der folgenden Datenschutzerklärung möchten wir Sie darüber aufklären, welche Arten Ihrer
                    personenbezogenen Daten (nachfolgend auch kurz als "Daten“ bezeichnet) wir zu welchen Zwecken und in
                    welchem Umfang im Rahmen der Bereitstellung unserer Applikation verarbeiten.</p>

                <p>Die verwendeten Begriffe sind nicht geschlechtsspezifisch.</p>

                <p>Stand: 5. Oktober 2021</p>
                <h2>Inhaltsübersicht</h2>
                <ul class="index">
                    <li><a class="index-link" href="#m1870">Einleitung</a></li>
                    <li><a class="index-link" href="#m3">Verantwortlicher</a></li>
                    <li><a class="index-link" href="#mOverview">Übersicht der Verarbeitungen</a></li>
                    <li><a class="index-link" href="#m13">Maßgebliche Rechtsgrundlagen</a></li>
                    <li><a class="index-link" href="#m27">Sicherheitsmaßnahmen</a></li>
                    <li><a class="index-link" href="#m12">Löschung von Daten</a></li>
                    <li><a class="index-link" href="#m134">Einsatz von Cookies</a></li>
                    <li><a class="index-link" href="#m225">Bereitstellung des Onlineangebotes und Webhosting</a></li>
                    <li><a class="index-link" href="#m451">Single-Sign-On-Anmeldung</a></li>
                    <li><a class="index-link" href="#m17">Newsletter und elektronische Benachrichtigungen</a></li>
                    <li><a class="index-link" href="#m328">Plugins und eingebettete Funktionen sowie Inhalte</a></li>
                </ul>
                <h2 id="m3">Verantwortlicher</h2>


                <p>{{env('impressum.name')}}
                    <br />{{env('impressum.street')}} {{env('impressum.number')}}
                    <br />{{env('impressum.zip')}} {{env('impressum.city')}}</p>

                <p>E-Mail-Adresse: <a href="mailto:{{ env('impressum.mail') }}">{{ env('impressum.mail') }}</a>.</p>

                <p>Telefon: {{env('impressum.phone')}}</p>

                <p>Impressum: <a href="{{ route('legal.impressum') }}" target="_blank">{{ route('legal.impressum') }}</a>.
                </p>

                <h2 id="mOverview">Übersicht der Verarbeitungen</h2>
                <p>Die nachfolgende Übersicht fasst die Arten der verarbeiteten Daten und die Zwecke ihrer Verarbeitung
                    zusammen und verweist auf die betroffenen Personen.</p>
                <h3>Arten der verarbeiteten Daten</h3>

                <ul>
                    <li>Bestandsdaten (z.B. Namen, Adressen).</li>
                    <li>Inhaltsdaten (z.B. Eingaben in Onlineformularen).</li>
                    <li>Kontaktdaten (z.B. E-Mail, Telefonnummern).</li>
                    <li>Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).</li>
                    <li>Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten).</li>
                </ul>
                <h3>Kategorien betroffener Personen</h3>
                <ul>
                    <li>Kommunikationspartner.</li>
                    <li>Nutzer (z.B. Webseitenbesucher, Nutzer von Onlinediensten).</li>
                </ul>
                <h3>Zwecke der Verarbeitung</h3>
                <ul>
                    <li>Anmeldeverfahren.</li>
                    <li>Bereitstellung unseres Onlineangebotes und Nutzerfreundlichkeit.</li>
                    <li>Direktmarketing (z.B. per E-Mail oder postalisch).</li>
                    <li>Kontaktanfragen und Kommunikation.</li>
                    <li>Erbringung vertraglicher Leistungen und Kundenservice.</li>
                </ul>
                <h3 id="m13">Maßgebliche Rechtsgrundlagen</h3>
                <p>Im Folgenden erhalten Sie eine Übersicht der Rechtsgrundlagen der DSGVO, auf deren Basis wir
                    personenbezogene Daten verarbeiten. Bitte nehmen Sie zur Kenntnis, dass neben den Regelungen der
                    DSGVO nationale Datenschutzvorgaben in Ihrem bzw. unserem Wohn- oder Sitzland gelten können. Sollten
                    ferner im Einzelfall speziellere Rechtsgrundlagen maßgeblich sein, teilen wir Ihnen diese in der
                    Datenschutzerklärung mit.</p>

                <ul>
                    <li><strong>Einwilligung (Art. 6 Abs. 1 S. 1 lit. a. DSGVO)</strong> - Die betroffene Person hat
                        ihre Einwilligung in die Verarbeitung der sie betreffenden personenbezogenen Daten für einen
                        spezifischen Zweck oder mehrere bestimmte Zwecke gegeben.
                    </li>
                    <li><strong>Vertragserfüllung und vorvertragliche Anfragen (Art. 6 Abs. 1 S. 1 lit. b.
                            DSGVO)</strong> - Die Verarbeitung ist für die Erfüllung eines Vertrags, dessen
                        Vertragspartei die betroffene Person ist, oder zur Durchführung vorvertraglicher Maßnahmen
                        erforderlich, die auf Anfrage der betroffenen Person erfolgen.
                    </li>
                    <li><strong>Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO)</strong> - Die Verarbeitung
                        ist zur Wahrung der berechtigten Interessen des Verantwortlichen oder eines Dritten
                        erforderlich, sofern nicht die Interessen oder Grundrechte und Grundfreiheiten der betroffenen
                        Person, die den Schutz personenbezogener Daten erfordern, überwiegen.
                    </li>
                </ul>
                <p><strong>Nationale Datenschutzregelungen in Deutschland</strong>: Zusätzlich zu den
                    Datenschutzregelungen der Datenschutz-Grundverordnung gelten nationale Regelungen zum Datenschutz in
                    Deutschland. Hierzu gehört insbesondere das Gesetz zum Schutz vor Missbrauch personenbezogener Daten
                    bei der Datenverarbeitung (Bundesdatenschutzgesetz – BDSG). Das BDSG enthält insbesondere
                    Spezialregelungen zum Recht auf Auskunft, zum Recht auf Löschung, zum Widerspruchsrecht, zur
                    Verarbeitung besonderer Kategorien personenbezogener Daten, zur Verarbeitung für andere Zwecke und
                    zur Übermittlung sowie automatisierten Entscheidungsfindung im Einzelfall einschließlich Profiling.
                    Des Weiteren regelt es die Datenverarbeitung für Zwecke des Beschäftigungsverhältnisses (§ 26 BDSG),
                    insbesondere im Hinblick auf die Begründung, Durchführung oder Beendigung von
                    Beschäftigungsverhältnissen sowie die Einwilligung von Beschäftigten. Ferner können
                    Landesdatenschutzgesetze der einzelnen Bundesländer zur Anwendung gelangen.</p>

                <h2 id="m27">Sicherheitsmaßnahmen</h2>
                <p>Wir treffen nach Maßgabe der gesetzlichen Vorgaben unter Berücksichtigung des Stands der Technik, der
                    Implementierungskosten und der Art, des Umfangs, der Umstände und der Zwecke der Verarbeitung sowie
                    der unterschiedlichen Eintrittswahrscheinlichkeiten und des Ausmaßes der Bedrohung der Rechte und
                    Freiheiten natürlicher Personen geeignete technische und organisatorische Maßnahmen, um ein dem
                    Risiko angemessenes Schutzniveau zu gewährleisten.</p>

                <p>Zu den Maßnahmen gehören insbesondere die Sicherung der Vertraulichkeit, Integrität und Verfügbarkeit
                    von Daten durch Kontrolle des physischen und elektronischen Zugangs zu den Daten als auch des sie
                    betreffenden Zugriffs, der Eingabe, der Weitergabe, der Sicherung der Verfügbarkeit und ihrer
                    Trennung. Des Weiteren haben wir Verfahren eingerichtet, die eine Wahrnehmung von
                    Betroffenenrechten, die Löschung von Daten und Reaktionen auf die Gefährdung der Daten
                    gewährleisten. Ferner berücksichtigen wir den Schutz personenbezogener Daten bereits bei der
                    Entwicklung bzw. Auswahl von Hardware, Software sowie Verfahren entsprechend dem Prinzip des
                    Datenschutzes, durch Technikgestaltung und durch datenschutzfreundliche Voreinstellungen.</p>

                <p><strong>SSL-Verschlüsselung (https)</strong>: Um Ihre via unserem Online-Angebot übermittelten Daten
                    zu schützen, nutzen wir eine SSL-Verschlüsselung. Sie erkennen derart verschlüsselte Verbindungen an
                    dem Präfix https:// in der Adresszeile Ihres Browsers.</p>

                <h2 id="m12">Löschung von Daten</h2>
                <p>Die von uns verarbeiteten Daten werden nach Maßgabe der gesetzlichen Vorgaben gelöscht, sobald deren
                    zur Verarbeitung erlaubten Einwilligungen widerrufen werden oder sonstige Erlaubnisse entfallen
                    (z.B. wenn der Zweck der Verarbeitung dieser Daten entfallen ist oder sie für den Zweck nicht
                    erforderlich sind).</p>

                <p>Sofern die Daten nicht gelöscht werden, weil sie für andere und gesetzlich zulässige Zwecke
                    erforderlich sind, wird deren Verarbeitung auf diese Zwecke beschränkt. D.h., die Daten werden
                    gesperrt und nicht für andere Zwecke verarbeitet. Das gilt z.B. für Daten, die aus handels- oder
                    steuerrechtlichen Gründen aufbewahrt werden müssen oder deren Speicherung zur Geltendmachung,
                    Ausübung oder Verteidigung von Rechtsansprüchen oder zum Schutz der Rechte einer anderen natürlichen
                    oder juristischen Person erforderlich ist.</p>

                <p>Unsere Datenschutzhinweise können ferner weitere Angaben zu der Aufbewahrung und Löschung von Daten
                    beinhalten, die für die jeweiligen Verarbeitungen vorrangig gelten.</p>

                <h2 id="m134">Einsatz von Cookies</h2>
                <p>Cookies sind Textdateien, die Daten von besuchten Websites oder Domains enthalten und von einem
                    Browser auf dem Computer des Benutzers gespeichert werden. Ein Cookie dient in erster Linie dazu,
                    die Informationen über einen Benutzer während oder nach seinem Besuch innerhalb eines
                    Onlineangebotes zu speichern. Zu den gespeicherten Angaben können z.B. die Spracheinstellungen auf
                    einer Webseite, der Loginstatus, ein Warenkorb oder die Stelle, an der ein Video geschaut wurde,
                    gehören. Zu dem Begriff der Cookies zählen wir ferner andere Technologien, die die gleichen
                    Funktionen wie Cookies erfüllen (z.B. wenn Angaben der Nutzer anhand pseudonymer
                    Onlinekennzeichnungen gespeichert werden, auch als "Nutzer-IDs" bezeichnet)</p>

                <p><strong>Die folgenden Cookie-Typen und Funktionen werden unterschieden:</strong></p>
                <ul>
                    <li><strong>Temporäre Cookies (auch: Session- oder Sitzungs-Cookies):</strong>&nbsp;Temporäre
                        Cookies werden spätestens gelöscht, nachdem ein Nutzer ein Online-Angebot verlassen und seinen
                        Browser geschlossen hat.
                    </li>
                    <li><strong>Permanente Cookies:</strong>&nbsp;Permanente Cookies bleiben auch nach dem Schließen des
                        Browsers gespeichert. So kann beispielsweise der Login-Status gespeichert oder bevorzugte
                        Inhalte direkt angezeigt werden, wenn der Nutzer eine Website erneut besucht. Ebenso können die
                        Interessen von Nutzern, die zur Reichweitenmessung oder zu Marketingzwecken verwendet werden, in
                        einem solchen Cookie gespeichert werden.
                    </li>
                    <li><strong>First-Party-Cookies:</strong>&nbsp;First-Party-Cookies werden von uns selbst gesetzt.
                    </li>
                    <li><strong>Third-Party-Cookies (auch: Drittanbieter-Cookies)</strong>: Drittanbieter-Cookies werden
                        hauptsächlich von Werbetreibenden (sog. Dritten) verwendet, um Benutzerinformationen zu
                        verarbeiten.
                    </li>
                    <li><strong>Notwendige (auch: essentielle oder unbedingt erforderliche) Cookies:</strong> Cookies
                        können zum einen für den Betrieb einer Webseite unbedingt erforderlich sein (z.B. um Logins oder
                        andere Nutzereingaben zu speichern oder aus Gründen der Sicherheit).
                    </li>
                    <li><strong>Statistik-, Marketing- und Personalisierungs-Cookies</strong>: Ferner werden Cookies im
                        Regelfall auch im Rahmen der Reichweitenmessung eingesetzt sowie dann, wenn die Interessen eines
                        Nutzers oder sein Verhalten (z.B. Betrachten bestimmter Inhalte, Nutzen von Funktionen etc.) auf
                        einzelnen Webseiten in einem Nutzerprofil gespeichert werden. Solche Profile dienen dazu, den
                        Nutzern z.B. Inhalte anzuzeigen, die ihren potentiellen Interessen entsprechen. Dieses Verfahren
                        wird auch als "Tracking", d.h., Nachverfolgung der potentiellen Interessen der Nutzer
                        bezeichnet. Soweit wir Cookies oder "Tracking"-Technologien einsetzen, informieren wir Sie
                        gesondert in unserer Datenschutzerklärung oder im Rahmen der Einholung einer Einwilligung.
                    </li>
                </ul>
                <p><strong>Hinweise zu Rechtsgrundlagen: </strong> Auf welcher Rechtsgrundlage wir Ihre
                    personenbezogenen Daten mit Hilfe von Cookies verarbeiten, hängt davon ab, ob wir Sie um eine
                    Einwilligung bitten. Falls dies zutrifft und Sie in die Nutzung von Cookies einwilligen, ist die
                    Rechtsgrundlage der Verarbeitung Ihrer Daten die erklärte Einwilligung. Andernfalls werden die
                    mithilfe von Cookies verarbeiteten Daten auf Grundlage unserer berechtigten Interessen (z.B. an
                    einem betriebswirtschaftlichen Betrieb unseres Onlineangebotes und dessen Verbesserung) verarbeitet
                    oder, wenn der Einsatz von Cookies erforderlich ist, um unsere vertraglichen Verpflichtungen zu
                    erfüllen.</p>

                <p><strong>Speicherdauer: </strong>Sofern wir Ihnen keine expliziten Angaben zur Speicherdauer von
                    permanenten Cookies mitteilen (z. B. im Rahmen eines sog. Cookie-Opt-Ins), gehen Sie bitte davon
                    aus, dass die Speicherdauer bis zu zwei Jahre betragen kann.</p>

                <p><strong>Verwendete Cookies: </strong>Wir verwenden auf unserer Webseite ausschließlich technisch notwendige Cookies. Diese notwendige Cookies helfen dabei, die Webseite nutzbar zu machen, indem sie Grundfunktionen wie Zugriff auf sichere Bereiche der Webseite ermöglichen. Die Webseite kann ohne diese Cookies nicht richtig funktionieren. Die von uns eingesetzten Cookies dienen ausschließlich dazu, Sie zu identifizieren und persönliche Einstellungen zu sepeichern.</p>

                {{--
                <p><strong>Allgemeine Hinweise zum Widerruf und Widerspruch (Opt-Out): </strong> Abhängig davon, ob die
                    Verarbeitung auf Grundlage einer Einwilligung oder gesetzlichen Erlaubnis erfolgt, haben Sie
                    jederzeit die Möglichkeit, eine erteilte Einwilligung zu widerrufen oder der Verarbeitung Ihrer
                    Daten durch Cookie-Technologien zu widersprechen (zusammenfassend als "Opt-Out" bezeichnet). Sie
                    können Ihren Widerspruch zunächst mittels der Einstellungen Ihres Browsers erklären, z.B., indem Sie
                    die Nutzung von Cookies deaktivieren (wobei hierdurch auch die Funktionsfähigkeit unseres
                    Onlineangebotes eingeschränkt werden kann). Ein Widerspruch gegen den Einsatz von Cookies zu Zwecken
                    des Onlinemarketings kann auch mittels einer Vielzahl von Diensten, vor allem im Fall des Trackings,
                    über die Webseiten <a href="https://optout.aboutads.info" target="_blank">https://optout.aboutads.info</a>
                    und <a href="https://www.youronlinechoices.com/"
                           target="_blank">https://www.youronlinechoices.com/</a> erklärt werden. Daneben können Sie
                    weitere Widerspruchshinweise im Rahmen der Angaben zu den eingesetzten Dienstleistern und Cookies
                    erhalten.</p>
                --}}
                <ul class="m-elements">
                    <li><strong>Verarbeitete Datenarten:</strong> Nutzungsdaten (z.B. besuchte Webseiten, Interesse an
                        Inhalten, Zugriffszeiten), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).
                    </li>
                    <li><strong>Betroffene Personen:</strong> Nutzer (z.B. Webseitenbesucher, Nutzer von
                        Onlinediensten).
                    </li>
                    <li><strong>Rechtsgrundlagen:</strong> Einwilligung (Art. 6 Abs. 1 S. 1 lit. a. DSGVO), Berechtigte
                        Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).
                    </li>
                </ul>
                <h2 id="m225">Bereitstellung des Onlineangebotes und Webhosting</h2>
                <p>Um unser Onlineangebot sicher und effizient bereitstellen zu können, nehmen wir die Leistungen von
                    einem oder mehreren Webhosting-Anbietern in Anspruch, von deren Servern (bzw. von ihnen verwalteten
                    Servern) das Onlineangebot abgerufen werden kann. Zu diesen Zwecken können wir Infrastruktur- und
                    Plattformdienstleistungen, Rechenkapazität, Speicherplatz und Datenbankdienste sowie
                    Sicherheitsleistungen und technische Wartungsleistungen in Anspruch nehmen.</p>

                <p>Zu den im Rahmen der Bereitstellung des Hostingangebotes verarbeiteten Daten können alle die Nutzer
                    unseres Onlineangebotes betreffenden Angaben gehören, die im Rahmen der Nutzung und der
                    Kommunikation anfallen. Hierzu gehören regelmäßig die IP-Adresse, die notwendig ist, um die Inhalte
                    von Onlineangeboten an Browser ausliefern zu können, und alle innerhalb unseres Onlineangebotes oder
                    von Webseiten getätigten Eingaben.</p>

                <p><strong>Erhebung von Zugriffsdaten und Logfiles</strong>: Wir selbst (bzw. unser Webhostinganbieter)
                    erheben Daten zu jedem Zugriff auf den Server (sogenannte Serverlogfiles). Zu den Serverlogfiles
                    können die Adresse und Name der abgerufenen Webseiten und Dateien, Datum und Uhrzeit des Abrufs,
                    übertragene Datenmengen, Meldung über erfolgreichen Abruf, Browsertyp nebst Version, das
                    Betriebssystem des Nutzers, Referrer URL (die zuvor besuchte Seite) und im Regelfall IP-Adressen und
                    der anfragende Provider gehören.</p>

                <p>Die Serverlogfiles können zum einen zu Zwecken der Sicherheit eingesetzt werden, z.B., um eine
                    Überlastung der Server zu vermeiden (insbesondere im Fall von missbräuchlichen Angriffen,
                    sogenannten DDoS-Attacken) und zum anderen, um die Auslastung der Server und ihre Stabilität
                    sicherzustellen.</p>

                <ul class="m-elements">
                    <li><strong>Verarbeitete Datenarten:</strong> Inhaltsdaten (z.B. Eingaben in Onlineformularen),
                        Nutzungsdaten (z.B. besuchte Webseiten, Interesse an Inhalten, Zugriffszeiten),
                        Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).
                    </li>
                    <li><strong>Betroffene Personen:</strong> Nutzer (z.B. Webseitenbesucher, Nutzer von
                        Onlinediensten).
                    </li>
                    <li><strong>Zwecke der Verarbeitung:</strong> Bereitstellung unseres Onlineangebotes und
                        Nutzerfreundlichkeit.
                    </li>
                    <li><strong>Rechtsgrundlagen:</strong> Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).
                    </li>
                </ul>
                <p><strong>Eingesetzte Dienste und Diensteanbieter:</strong></p>
                <ul class="m-elements">
                    <li><strong>netcup:</strong> Leistungen auf dem Gebiet der Bereitstellung von
                        informationstechnischer Infrastruktur und verbundenen Dienstleistungen (z.B. Speicherplatz
                        und/oder Rechenkapazitäten); Dienstanbieter: netcup GmbH, Daimlerstraße 25, D-76185 Karlsruhe,
                        Deutschland; Website: <a href="https://www.netcup.de/"
                                                 target="_blank">https://www.netcup.de/</a>; Datenschutzerklärung: <a
                                href="https://www.netcup.de/kontakt/datenschutzerklaerung.php" target="_blank">https://www.netcup.de/kontakt/datenschutzerklaerung.php</a>.
                    </li>
                </ul>
                <h2 id="m451">Single-Sign-On-Anmeldung</h2>
                <p>Als "Single-Sign-On“ oder "Single-Sign-On-Anmeldung bzw. "-Authentifizierung“ werden Verfahren
                    bezeichnet, die es Nutzern erlauben, sich mit Hilfe eines Nutzerkontos bei einem Anbieter von
                    Single-Sign-On-Verfahren (z.B. einem sozialen Netzwerk), auch bei unserem Onlineangebot, anzumelden.
                    Voraussetzung der Single-Sign-On-Authentifizierung ist, dass die Nutzer bei dem jeweiligen
                    Single-Sign-On-Anbieter registriert sind und die erforderlichen Zugangsdaten in dem dafür
                    vorgesehenen Onlineformular eingeben, bzw. schon bei dem Single-Sign-On-Anbieter angemeldet sind und
                    die Single-Sign-On-Anmeldung via Schaltfläche bestätigen.</p>

                <p>Die Authentifizierung erfolgt direkt bei dem jeweiligen Single-Sign-On-Anbieter. Im Rahmen einer
                    solchen Authentifizierung erhalten wir eine Nutzer-ID mit der Information, dass der Nutzer unter
                    dieser Nutzer-ID beim jeweiligen Single-Sign-On-Anbieter eingeloggt ist und eine für uns für andere
                    Zwecke nicht weiter nutzbare ID (sog "User Handle“). Ob uns zusätzliche Daten übermittelt werden,
                    hängt allein von dem genutzten Single-Sign-On-Verfahren ab, von den gewählten Datenfreigaben im
                    Rahmen der Authentifizierung und zudem davon, welche Daten Nutzer in den Privatsphäre- oder
                    sonstigen Einstellungen des Nutzerkontos beim Single-Sign-On-Anbieter freigegeben haben. Es können
                    je nach Single-Sign-On-Anbieter und der Wahl der Nutzer verschiedene Daten sein, in der Regel sind
                    es die E-Mail-Adresse und der Benutzername. Das im Rahmen des Single-Sign-On-Verfahrens eingegebene
                    Passwort bei dem Single-Sign-On-Anbieter ist für uns weder einsehbar, noch wird es von uns
                    gespeichert. </p>

                <p>Die Nutzer werden gebeten, zu beachten, dass deren bei uns gespeicherte Angaben automatisch mit ihrem
                    Nutzerkonto beim Single-Sign-On-Anbieter abgeglichen werden können, dies jedoch nicht immer möglich
                    ist oder tatsächlich erfolgt. Ändern sich z.B. die E-Mail-Adressen der Nutzer, müssen sie diese
                    manuell in ihrem Nutzerkonto bei uns ändern.</p>

                <p>Die Single-Sign-On-Anmeldung können wir, sofern mit den Nutzern vereinbart, im Rahmen der oder vor
                    der Vertragserfüllung einsetzen, soweit die Nutzer darum gebeten wurden, im Rahmen einer
                    Einwilligung verarbeiten und setzen sie ansonsten auf Grundlage der berechtigten Interessen
                    unsererseits und der Interessen der Nutzer an einem effektiven und sicheren Anmeldesystem ein.</p>

                <p>Sollten Nutzer sich einmal entscheiden, die Verknüpfung ihres Nutzerkontos beim
                    Single-Sign-On-Anbieter nicht mehr für das Single-Sign-On-Verfahren nutzen zu wollen, müssen sie
                    diese Verbindung innerhalb ihres Nutzerkontos beim Single-Sign-On-Anbieter aufheben. Möchten Nutzer
                    deren Daten bei uns löschen, müssen sie ihre Registrierung bei uns kündigen.</p>

                <p><strong>Facebook Single-Sign-On</strong>: Wir sind gemeinsam mit Facebook Irland Ltd. für die
                    Erhebung oder den Erhalt im Rahmen einer Übermittlung (jedoch nicht die weitere Verarbeitung) von
                    "Event-Daten", die Facebook mittels der Facebook-Single-Sign-On-Anmeldeverfahren, die auf unserem
                    Onlineangebot ausgeführt werden, erhebt oder im Rahmen einer Übermittlung zu folgenden Zwecken
                    erhält, gemeinsam verantwortlich: a) Anzeige von Inhalten Werbeinformationen, die den mutmaßlichen
                    Interessen der Nutzer entsprechen; b) Zustellung kommerzieller und transaktionsbezogener Nachrichten
                    (z. B. Ansprache von Nutzern via Facebook-Messenger); c) Verbesserung der Anzeigenauslieferung und
                    Personalisierung von Funktionen und Inhalten (z. B. Verbesserung der Erkennung, welche Inhalte oder
                    Werbeinformationen mutmaßlich den Interessen der Nutzer entsprechen). Wir haben mit Facebook eine
                    spezielle Vereinbarung abgeschlossen ("Zusatz für Verantwortliche", <a
                            href="https://www.facebook.com/legal/controller_addendum" target="_blank">https://www.facebook.com/legal/controller_addendum</a>),
                    in der insbesondere geregelt wird, welche Sicherheitsmaßnahmen Facebook beachten muss (<a
                            href="https://www.facebook.com/legal/terms/data_security_terms" target="_blank">https://www.facebook.com/legal/terms/data_security_terms</a>)
                    und in der Facebook sich bereit erklärt hat die Betroffenenrechte zu erfüllen (d. h. Nutzer können
                    z. B. Auskünfte oder Löschungsanfragen direkt an Facebook richten). Hinweis: Wenn Facebook uns
                    Messwerte, Analysen und Berichte bereitstellt (die aggregiert sind, d. h. keine Angaben zu einzelnen
                    Nutzern erhalten und für uns anonym sind), dann erfolgt diese Verarbeitung nicht im Rahmen der
                    gemeinsamen Verantwortlichkeit, sondern auf Grundlage eines Auftragsverarbeitungsvertrages
                    ("Datenverarbeitungsbedingungen ", <a href="https://www.facebook.com/legal/terms/dataprocessing"
                                                          target="_blank">https://www.facebook.com/legal/terms/dataprocessing</a>),
                    der "Datensicherheitsbedingungen" (<a
                            href="https://www.facebook.com/legal/terms/data_security_terms" target="_blank">https://www.facebook.com/legal/terms/data_security_terms</a>)
                    sowie im Hinblick auf die Verarbeitung in den USA auf Grundlage von Standardvertragsklauseln
                    ("Facebook-EU-Datenübermittlungszusatz, <a
                            href="https://www.facebook.com/legal/EU_data_transfer_addendum" target="_blank">https://www.facebook.com/legal/EU_data_transfer_addendum</a>).
                    Die Rechte der Nutzer (insbesondere auf Auskunft, Löschung, Widerspruch und Beschwerde bei
                    zuständiger Aufsichtsbehörde), werden durch die Vereinbarungen mit Facebook nicht eingeschränkt.</p>

                <ul class="m-elements">
                    <li><strong>Verarbeitete Datenarten:</strong> Bestandsdaten (z.B. Namen, Adressen), Kontaktdaten
                        (z.B. E-Mail, Telefonnummern), Event-Daten (Facebook) ("Event-Daten" sind Daten, die z. B. via
                        Facebook-Pixel (via Apps oder auf anderen Wegen) von uns an Facebook übermittelt werden können
                        und sich auf Personen oder deren Handlungen beziehen; Zu den Daten gehören z. B. Angaben über
                        Besuche auf Websites, Interaktionen mit Inhalten, Funktionen, Installationen von Apps, Käufe von
                        Produkten, etc.; die Event-Daten werden zwecks Bildung von Zielgruppen für Inhalte und
                        Werbeinformationen (Custom Audiences) verarbeitet; Event Daten beinhalten nicht die eigentlichen
                        Inhalte (wie z. B. verfasste Kommentare), keine Login-Informationen und keine
                        Kontaktinformationen (also keine Namen, E-Mail-Adressen und Telefonnummern). Event Daten werden
                        durch Facebook nach maximal zwei Jahren gelöscht, die aus ihnen gebildeten Zielgruppen mit der
                        Löschung unseres Facebook-Kontos).
                    </li>
                    <li><strong>Betroffene Personen:</strong> Nutzer (z.B. Webseitenbesucher, Nutzer von
                        Onlinediensten).
                    </li>
                    <li><strong>Zwecke der Verarbeitung:</strong> Erbringung vertraglicher Leistungen und Kundenservice,
                        Anmeldeverfahren.
                    </li>
                    <li><strong>Rechtsgrundlagen:</strong> Einwilligung (Art. 6 Abs. 1 S. 1 lit. a. DSGVO),
                        Vertragserfüllung und vorvertragliche Anfragen (Art. 6 Abs. 1 S. 1 lit. b. DSGVO), Berechtigte
                        Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).
                    </li>
                </ul>
                <p><strong>Eingesetzte Dienste und Diensteanbieter:</strong></p>
                <ul class="m-elements">
                    <li><strong>Facebook Single-Sign-On:</strong> Authentifizierungsdienst; Dienstanbieter: <a
                                href="https://www.facebook.com" target="_blank">https://www.facebook.com</a>, Facebook
                        Ireland Ltd., 4 Grand Canal Square, Grand Canal Harbour, Dublin 2, Irland, Mutterunternehmen:
                        Facebook, 1 Hacker Way, Menlo Park, CA 94025, USA; Website: <a href="https://www.facebook.com"
                                                                                       target="_blank">https://www.facebook.com</a>;
                        Datenschutzerklärung: <a href="https://www.facebook.com/about/privacy" target="_blank">https://www.facebook.com/about/privacy</a>;
                        Widerspruchsmöglichkeit (Opt-Out): <a href="https://www.facebook.com/adpreferences/ad_settings"
                                                              target="_blank">https://www.facebook.com/adpreferences/ad_settings</a>
                        (Login bei Facebook ist erforderlich).
                    </li>
                </ul>
                <h2 id="m17">Newsletter und elektronische Benachrichtigungen</h2>
                <p>Wir versenden Newsletter, E-Mails und weitere elektronische Benachrichtigungen (nachfolgend
                    "Newsletter“) nur mit der Einwilligung der Empfänger oder einer gesetzlichen Erlaubnis. Sofern im
                    Rahmen einer Anmeldung zum Newsletter dessen Inhalte konkret umschrieben werden, sind sie für die
                    Einwilligung der Nutzer maßgeblich. Im Übrigen enthalten unsere Newsletter Informationen zu unseren
                    Leistungen und uns.</p>

                <p>Um sich zu unseren Newslettern anzumelden, reicht es grundsätzlich aus, wenn Sie Ihre E-Mail-Adresse
                    angeben. Wir können Sie jedoch bitten, einen Namen, zwecks persönlicher Ansprache im Newsletter,
                    oder weitere Angaben, sofern diese für die Zwecke des Newsletters erforderlich sind, zu tätigen.</p>

                <p><strong>Double-Opt-In-Verfahren:</strong> Die Anmeldung zu unserem Newsletter erfolgt grundsätzlich
                    in einem sogenannte Double-Opt-In-Verfahren. D.h., Sie erhalten nach der Anmeldung eine E-Mail, in
                    der Sie um die Bestätigung Ihrer Anmeldung gebeten werden. Diese Bestätigung ist notwendig, damit
                    sich niemand mit fremden E-Mail-Adressen anmelden kann. Die Anmeldungen zum Newsletter werden
                    protokolliert, um den Anmeldeprozess entsprechend den rechtlichen Anforderungen nachweisen zu
                    können. Hierzu gehört die Speicherung des Anmelde- und des Bestätigungszeitpunkts als auch der
                    IP-Adresse. Ebenso werden die Änderungen Ihrer bei dem Versanddienstleister gespeicherten Daten
                    protokolliert.</p>

                <p><strong>Löschung und Einschränkung der Verarbeitung: </strong> Wir können die ausgetragenen
                    E-Mail-Adressen bis zu drei Jahren auf Grundlage unserer berechtigten Interessen speichern, bevor
                    wir sie löschen, um eine ehemals gegebene Einwilligung nachweisen zu können. Die Verarbeitung dieser
                    Daten wird auf den Zweck einer möglichen Abwehr von Ansprüchen beschränkt. Ein individueller
                    Löschungsantrag ist jederzeit möglich, sofern zugleich das ehemalige Bestehen einer Einwilligung
                    bestätigt wird. Im Fall von Pflichten zur dauerhaften Beachtung von Widersprüchen behalten wir uns
                    die Speicherung der E-Mail-Adresse alleine zu diesem Zweck in einer Sperrliste (sogenannte
                    "Blocklist") vor.</p>

                <p>Die Protokollierung des Anmeldeverfahrens erfolgt auf Grundlage unserer berechtigten Interessen zu
                    Zwecken des Nachweises seines ordnungsgemäßen Ablaufs. Soweit wir einen Dienstleister mit dem
                    Versand von E-Mails beauftragen, erfolgt dies auf Grundlage unserer berechtigten Interessen an einem
                    effizienten und sicheren Versandsystem.</p>

                <p><strong>Hinweise zu Rechtsgrundlagen:</strong> Der Versand der Newsletter erfolgt auf Grundlage einer
                    Einwilligung der Empfänger oder, falls eine Einwilligung nicht erforderlich ist, auf Grundlage
                    unserer berechtigten Interessen am Direktmarketing, sofern und soweit diese gesetzlich, z.B. im Fall
                    von Bestandskundenwerbung, erlaubt ist. Soweit wir einen Dienstleister mit dem Versand von E-Mails
                    beauftragen, geschieht dies auf der Grundlage unserer berechtigten Interessen. Das
                    Registrierungsverfahren wird auf der Grundlage unserer berechtigten Interessen aufgezeichnet, um
                    nachzuweisen, dass es in Übereinstimmung mit dem Gesetz durchgeführt wurde.</p>

                <p>Inhalte: Informationen zu News der Gliederung als auch des Dienstplan-Portals</p>

                <ul class="m-elements">
                    <li><strong>Verarbeitete Datenarten:</strong> Bestandsdaten (z.B. Namen, Adressen), Kontaktdaten
                        (z.B. E-Mail, Telefonnummern), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen,
                        IP-Adressen).
                    </li>
                    <li><strong>Betroffene Personen:</strong> Kommunikationspartner.</li>
                    <li><strong>Zwecke der Verarbeitung:</strong> Direktmarketing (z.B. per E-Mail oder postalisch).
                    </li>
                    <li><strong>Rechtsgrundlagen:</strong> Einwilligung (Art. 6 Abs. 1 S. 1 lit. a. DSGVO), Berechtigte
                        Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).
                    </li>
                    <li><strong>Widerspruchsmöglichkeit (Opt-Out):</strong> Sie können den Empfang unseres Newsletters
                        jederzeit kündigen, d.h. Ihre Einwilligungen widerrufen, bzw. dem weiteren Empfang
                        widersprechen. Einen Link zur Kündigung des Newsletters finden Sie entweder am Ende eines jeden
                        Newsletters oder können sonst eine der oben angegebenen Kontaktmöglichkeiten, vorzugswürdig
                        E-Mail, hierzu nutzen.
                    </li>
                </ul>
                <h2 id="m328">Plugins und eingebettete Funktionen sowie Inhalte</h2>
                <p>Wir binden in unser Onlineangebot Funktions- und Inhaltselemente ein, die von den Servern ihrer
                    jeweiligen Anbieter (nachfolgend bezeichnet als "Drittanbieter”) bezogen werden. Dabei kann es sich
                    zum Beispiel um Grafiken, Videos oder Stadtpläne handeln (nachfolgend einheitlich bezeichnet als
                    "Inhalte”).</p>

                <p>Die Einbindung setzt immer voraus, dass die Drittanbieter dieser Inhalte die IP-Adresse der Nutzer
                    verarbeiten, da sie ohne die IP-Adresse die Inhalte nicht an deren Browser senden könnten. Die
                    IP-Adresse ist damit für die Darstellung dieser Inhalte oder Funktionen erforderlich. Wir bemühen
                    uns, nur solche Inhalte zu verwenden, deren jeweilige Anbieter die IP-Adresse lediglich zur
                    Auslieferung der Inhalte verwenden. Drittanbieter können ferner sogenannte Pixel-Tags (unsichtbare
                    Grafiken, auch als "Web Beacons" bezeichnet) für statistische oder Marketingzwecke verwenden. Durch
                    die "Pixel-Tags" können Informationen, wie der Besucherverkehr auf den Seiten dieser Webseite,
                    ausgewertet werden. Die pseudonymen Informationen können ferner in Cookies auf dem Gerät der Nutzer
                    gespeichert werden und unter anderem technische Informationen zum Browser und zum Betriebssystem, zu
                    verweisenden Webseiten, zur Besuchszeit sowie weitere Angaben zur Nutzung unseres Onlineangebotes
                    enthalten als auch mit solchen Informationen aus anderen Quellen verbunden werden.</p>

                <p><strong>Hinweise zu Rechtsgrundlagen:</strong> Sofern wir die Nutzer um deren Einwilligung in den
                    Einsatz der Drittanbieter bitten, ist die Rechtsgrundlage der Verarbeitung von Daten die
                    Einwilligung. Ansonsten werden die Daten der Nutzer auf Grundlage unserer berechtigten Interessen
                    (d.h. Interesse an effizienten, wirtschaftlichen und empfängerfreundlichen Leistungen) verarbeitet.
                    In diesem Zusammenhang möchten wir Sie auch auf die Informationen zur Verwendung von Cookies in
                    dieser Datenschutzerklärung hinweisen.</p>

                <ul class="m-elements">
                    <li><strong>Verarbeitete Datenarten:</strong> Nutzungsdaten (z.B. besuchte Webseiten, Interesse an
                        Inhalten, Zugriffszeiten), Meta-/Kommunikationsdaten (z.B. Geräte-Informationen, IP-Adressen).
                    </li>
                    <li><strong>Betroffene Personen:</strong> Nutzer (z.B. Webseitenbesucher, Nutzer von
                        Onlinediensten).
                    </li>
                    <li><strong>Zwecke der Verarbeitung:</strong> Bereitstellung unseres Onlineangebotes und
                        Nutzerfreundlichkeit, Erbringung vertraglicher Leistungen und Kundenservice.
                    </li>
                    <li><strong>Rechtsgrundlagen:</strong> Berechtigte Interessen (Art. 6 Abs. 1 S. 1 lit. f. DSGVO).
                    </li>
                </ul>
                <p><strong>Eingesetzte Dienste und Diensteanbieter:</strong></p>
                <ul class="m-elements">
                    <li><strong>Font Awesome:</strong> Darstellung von Schriftarten und Symbolen; Dienstanbieter:
                        Fonticons, Inc. ,6 Porter Road Apartment 3R, Cambridge, MA 02140, USA; Website: <a
                                href="https://fontawesome.com/" target="_blank">https://fontawesome.com/</a>;
                        Datenschutzerklärung: <a href="https://fontawesome.com/privacy" target="_blank">https://fontawesome.com/privacy</a>.
                    </li>
                    <li><strong>Google Fonts:</strong> Wir binden die Schriftarten ("Google Fonts") des Anbieters Google
                        ein, wobei die Daten der Nutzer allein zu Zwecken der Darstellung der Schriftarten im Browser
                        der Nutzer verwendet werden. Die Einbindung erfolgt auf Grundlage unserer berechtigten
                        Interessen an einer technisch sicheren, wartungsfreien und effizienten Nutzung von Schriftarten,
                        deren einheitlicher Darstellung sowie unter Berücksichtigung möglicher lizenzrechtlicher
                        Restriktionen für deren Einbindung. Dienstanbieter: Google Ireland Limited, Gordon House, Barrow
                        Street, Dublin 4, Irland, Mutterunternehmen: Google LLC, 1600 Amphitheatre Parkway, Mountain
                        View, CA 94043, USA; Website: <a href="https://fonts.google.com/" target="_blank">https://fonts.google.com/</a>;
                        Datenschutzerklärung: <a href="https://policies.google.com/privacy" target="_blank">https://policies.google.com/privacy</a>.
                    </li>
                    <li><strong>OpenStreetMap:</strong> Wir binden die Landkarten des Dienstes "OpenStreetMap" ein, die
                        auf Grundlage der Open Data Commons Open Database Lizenz (ODbL) durch die OpenStreetMap
                        Foundation (OSMF) angeboten werden. Die Daten der Nutzer werden durch OpenStreetMap
                        ausschließlich zu Zwecken der Darstellung der Kartenfunktionen und zur Zwischenspeicherung der
                        gewählten Einstellungen verwendet. Zu diesen Daten können insbesondere IP-Adressen und
                        Standortdaten der Nutzer gehören, die jedoch nicht ohne deren Einwilligung (im Regelfall im
                        Rahmen der Einstellungen ihrer Mobilgeräte vollzogen) erhoben werden. Dienstanbieter:
                        OpenStreetMap Foundation (OSMF); Website: <a href="https://www.openstreetmap.de"
                                                                     target="_blank">https://www.openstreetmap.de</a>;
                        Datenschutzerklärung: <a href="https://wiki.openstreetmap.org/wiki/Privacy_Policy"
                                                 target="_blank">https://wiki.openstreetmap.org/wiki/Privacy_Policy</a>.
                    </li>
                </ul>
                <p class="seal"><a href="https://datenschutz-generator.de/?l=de"
                                   title="Rechtstext von Dr. Schwenke - für weitere Informationen bitte anklicken."
                                   target="_blank" rel="noopener noreferrer nofollow">Erstellt mit kostenlosem
                        Datenschutz-Generator.de von Dr. Thomas Schwenke</a></p>

            </div>
        </div>
        <div class="pull-right top-buffer">
            <a href="/impressum">Impressum</a>
            <a href="/datenschutz">Datenschutz</a>
        </div>
    </body>
@endsection