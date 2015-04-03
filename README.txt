== Konfigurasjon ==
Du må endre instillinger for database, migrering (key og server), payex betaling og snapporder-integrasjon.
 $ cp inside/credentials-sample.php inside/credentials.php
 $ cp inside/migration/config.example.php inside/migration/config.php
 $ cp includes/payex2/payex_defines.example.php includes/payex2/payex_defines.php
 $ cp snapporder/config-sample.php snapporder/config.php

Du trenger kanskje å endre ting i includes/Ldap_defines.php også.

== Installasjon ==
Legg inn Apache, MySQL og PHP. Du trenger også noen utvidelser til PHP.
 $ sudo apt-get install php5-mysql php5-ldap
