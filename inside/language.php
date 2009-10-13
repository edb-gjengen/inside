<?php

if ($_SESSION['language'] == "no"){
  define("ARTICLE", "nyhet");
  define("ARTICLES", "nyheter");
  define("BARSHIFT", "barvakt");
  define("BARSHIFTWORKER", "oppsatt barvakt");
  define("BUGREPORT", "feilrapport");
  define("DELETE", "slett");
  define("EDIT", "endre");
  define("JOB", "stilling");
  define("EVENT", "aktivitet");
  define("EVENTCOMMENT", "kommentar");
  define("CONCERT", "arrangement");
  define("CONCERTREPORT", "arrangementsrapport");
  define("GROUP", "gruppe");
  define("ACTION", "handling");
  define("DOCUMENT", "dokument");
  define("DIVISION", "forening");
  define("POSITION", "stillingsbeskrivelse");
  define("DOCCAT", "dokumenttype"); 
  define("EVENTCAT", "aktivitetstype");
  define("JOBCAT", "stillingstype");
  define("PRODUCT", "produkt");
  define("UPDATED", "oppdatert");
  define("MESSAGE", "beskjed");
  define("WEEKPROGRAM", "ukesprogram");
  define("ERROR", "problem");
  define("USERGROUPRELATIONSHIP", "gruppemedlemskap");

}else {
  define("ARTICLE", "article");
  define("ARTICLES", "articles");
  define("BARSHIFT", "bar shift");
  define("BARSHIFTWORKER", "registered bar shift");
  define("BUGREPORT", "bug report");
  define("DELETE", "delete");
  define("EDIT", "edit");
  define("JOB", "job");
  define("EVENT", "event");
  define("EVENTCOMMENT", "comment");
  define("CONCERT", "concert");
  define("CONCERTREPORT", "concert report");
  define("GROUP", "group");
  define("ACTION", "action");
  define("DOCUMENT", "document");
  define("DIVISION", "division");
  define("POSITION", "position");
  define("DOCCAT", "documentCategory");
  define("EVENTCAT", "eventCategory");
  define("JOBCAT", "jobCategory");
  define("PRODUCT", "product");
  define("UPDATED", "updated");
  define("MESSAGE", "message");
  define("WEEKPROGRAM", "weekProgram");
  define("ERROR", "error");
  define("USERGROUPRELATIONSHIP", "group membership");

}

?>