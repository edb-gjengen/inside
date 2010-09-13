<?php

$request = App_View_ViewHelper::getRequest();
$shift = $request->getObject("WorkShift");

include("Common/header.php");


function showDate($name) {
  print "<input type=\"text\" name=\"" . $name . "_date\" size=\"8\" . maxlength=\"10\" value=\"" . date("j/n/Y") . "\" />";
  print "<input type=\"text\" name=\"" . $name . "_time\" size=\"8\" . maxlength=\"10\" value=\"" . date("H:i") . "\" />";
}

?>

<div>
  <h1>Legg til nytt vakt</h1>
	
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
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>?cmd=<?=$request->getProperty("cmd")?>">  
      <table>
        <tr>
          <td class="label"><label for="title">Hva</label></td>
          <td><input type="text" name="title" id="title" value="<?=$request->getProperty("title")?>" size="20" maxlength="40" /></td>
        </tr>
        <tr>
          <td class="label"><label for="starts">Starter</label></td>
          <td>
            <input type="text" name="starts_date" value="<?=$request->getProperty("starts_date")?>" size="8" maxlength="10" />
            <input type="text" name="starts_time" value="<?=$request->getProperty("starts_time")?>" size="4" maxlength="5" />
          </td>
        </tr>
        <tr>
          <td class="label"><label for="ends">Slutter</label></td>
          <td>
            <input type="text" name="ends_date" value="<?=$request->getProperty("ends_date")?>" size="8" maxlength="10" />
            <input type="text" name="ends_time" value="<?=$request->getProperty("ends_time")?>" size="4" maxlength="5" />
          </td>
        </tr>
        <tr>
          <td class="label"><label for="repeats">Gjentagelser</label></td>
          <td>Gjentas ikke</td>
        </tr>
        <tr>
          <td class="label"><label for="location">Hvor</label></td>
          <td>
            <select name="location" id="location">
<?php 
  $request->getObject("locations")->rewind();
  while ($location = $request->getObject("locations")->next()) {
    print "<option value=\"" . $location->getId() . "\"";
    if ($location->getId() == $request->getProperty("location")) print " selected";
    print ">" . $location->getName() . "</option>";
  }
?>
            </select>
          </td>
        </tr>
        <tr>
          <td class="label"><label for="num_workers">Antall på jobb</label></td>
          <td><input type="text" name="num_workers" id="num_workers" value="<?=$request->getProperty("num_workers")?>" size="10" maxlength="20" /></td>
        </tr>
        <tr>
          <td class="label"><label for="comment">Evt. beskrivelse</label></td>
          <td><textarea rows="3" cols="30" name="comment" id="comment"><?=$request->getProperty("comment")?></textarea></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" value="legg til" class="submit" /></td>
        </tr>
      </table>   
    </form>
  </div>
</div>

<?php 
include("Common/footer.php");
?>