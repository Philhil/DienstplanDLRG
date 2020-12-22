<h1>Neuer Client wurde angefragt!</h1>

<h2>Gliederung:</h2>
client_billing: {{$order['client_billing']}}<br>
client_billing_address: {{$order['client_billing_address']}}<br>
package: {{$order["package"]}}<br>
Module:
@if(in_array('module_default', $order))- Modul Dienstplan + Grundfunktionen <br> @endif
@if(in_array('module_training', $order))- Modul Training <br> @endif
@if(in_array('module_stats', $order))- Modul Erweiterte Analyse und Auswertungen <br> @endif


<h2>Client:</h2>
Name: {{$client->name}} <br>
mailReplyAddress: {{$client->mailReplyAddress}} <br>
module_training: {{$client->module_training}} <br>
module_training_credit: {{$client->module_training_credit}} <br>

<h2>User:</h2>
Vorname: {{$user->first_name}} <br>
Name: {{$user->name}} <br>
EMail: {{$user->email}} <br>
Approved: {{$user->approved}} <br>