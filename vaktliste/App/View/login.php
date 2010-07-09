<?php

$request = App_View_ViewHelper::getRequest();

include("Common/header.php");
?>

<div>
  <h1>Velkommen til vaktlisten!</h1>
	<p>Du må være logget inn for å kunne gjøre noe her.</p>
	
<?php 
  if (count($request->getFeedback()) > 0) {
    print "<div class=\"messages\">\n";
    print " <h3>Beskjeder</h3>\n";
    print " <ul>\n";
    print "  <li>" . $request->getFeedbackString("</li>\n  <li>") . "</li>\n";
    print " </ul>\n";
    print "</div>\n";
  }
?>

  <div class="contentElem">
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>?cmd=login">  
      <table>
        <tr>
          <td class="label"><label for="username">brukernavn</label></td>
          <td><input type="text" name="username" id="username" size="12" maxlength="12" /></td>
        </tr>
        <tr>
          <td class="label"><label for="password">passord</label></td>
  
          <td><input type="password" name="password" id="password" size="12" maxlength="50" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" value="logg inn" class="submit" /></td>
        </tr>
      </table>   
    </form>
  </div>
    
  <p>Mangler du brukernavn eller passord? <a href="https://www.studentersamfundet.no/inside/">Få tilsendt brukernavn og passord hvis du er registrert fra før</a>, eller <a href="https://www.studentersamfundet.no/inside/index.php?page=register-user">registrér deg</a>!</p>
    
</div>

<?php 
include("Common/footer.php");
?>