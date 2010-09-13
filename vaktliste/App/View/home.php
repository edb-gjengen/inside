<?php 
include("Common/header.php");
?>

  <table width="1000" cellpadding="0" cellspacing="0" style="background-color: #616161;">
    <tr id="meny-top">
      <td><a href="">min side</a></td>
      <td>|</td>
      <td><a href="">aktiv</a></td>
      <td></td>
      <td><a href=""></a></td>
      <td></td>
      <td><a href=""></a></td>
      <td></td>
      <td><a href=""></a></td>
      <td></td>
      <td><a href="">logg ut</a></td>
      <td>&nbsp;</td>
    </tr>
  </table>
  
  <div id="wrap" style="width:100%;padding-top: 10px;">
    <div id="content">
      <div class="text-column">
        <h1>Velkommen til Studentersamfundet Inside!</h1>
        <p>Her kan du registrere medlemskapet du har kjøpt, kjøpe medlemskap gjennom vår nettbutikk og oppdatere adressen din.
        Se også hvilke foreninger som tar inn nye medlemmer, eller hva som skjer på studentersamfundet denne uken!</p>
        
        
        <div class="contentElem">
          <form   method="post" action="index.php?action=log-in">  
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
  
        <h3>Ny bruker?</h3>
        <p><a href="index.php?page=register-user">Registrér deg!</a></p>
  
        <br />
  
        <h3>Mangler du eller har du glemt brukernavn og passord?</h3>
        <p>Du kan få tilsendt brukernavn og resatt passordet ditt ved å oppgi medlemskortnummer eller epostadresse:</p>
        
          <fieldset>
            <legend>Tast inn epost eller medlemskortnummer for å få tilsendt brukernavn og passord:</legend>
            <form method="post" action="index.php?action=order-password">
              <input type="text" name="userid" />
              <input type="submit" value="send bestilling" />
            </form>
          </fieldset>
        <br />
  
        <h3>Noe som ikke virker?</h3>
        <p>Send epost til <a href="mailto:medlemskap@studentersamfundet.no">medlemskap@studentersamfundet.no</a>. Oppgi medlemskortnummer og en kort beskrivelse av hva som ikke virker, så skal vi hjelpe deg så fort vi kan.</p>
      </div> <!-- #text-colum -->
    </div><!-- #content -->
        
    <div id="left" class="sidebar">
      <div class="column-element">
        Meny
      </div>

<?php 
include("Common/footer.php");
?>