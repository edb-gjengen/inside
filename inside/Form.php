<?php

class Form {

  var $id;
  var $title;
  var $enctype;
  var $method;
  var $action;
  var $fields;
  var $readonly;

  function Form($title, $enctype, $method, $action, $fields, $id = NULL, $readonly = false){
    $this->__construct($title, $enctype, $method, $action, $fields, $id, $readonly);
  }

  function __construct($title, $enctype, $method, $action, $fields, $id = NULL, $readonly = false){
    $this->id       = $id;
    $this->title    = $title;
    $this->enctype  = $enctype;
    $this->fields   = $fields;
    $this->method   = $method;
    $this->action   = $action;
    $this->readonly = $readonly;
  }

  function display($type = NULL){?>
        <div class="contentElem">
<?php

    if ($type == NULL){
      if (isset($_SESSION['formtype'])){
        $type = $_SESSION['formtype'];
      }else {
        $type = "table";
      }
    $pos = strpos($_SERVER['REQUEST_URI'], "action");
    if ($pos === false){?>
        <!--<a class="btn btn-default" href="index.php?action=switch-formtype&amp;section=customize">bytt skjemastil</a>-->
    
<?php }
    }
    if ($type == "table"){
?>
          <form <?php ($this->id != NULL) ? print "id=\"$this->id\"" : print ""?> <?php ($this->enctype != NULL) ? print "enctype=\"$this->enctype\"" : print ""?> method="<?php print $this->method; ?>" action="<?php print $this->action; ?>">  
            <table>
              <tr>
                <th colspan="2"><?php print $this->title; ?></th>
              </tr>
<?php
   foreach ($this->fields as $row){
     if ($row['type'] == 'hidden'){?>
              <tr style="display: none"><?php
     }else {?>
              <tr>
<?php } ?>
                <td class="label"><?php 
             if (!($row['type'] == "radio" || $row['type'] == "checkbox" )){
          ?><label for="<?php print $row['attributes']['name']; ?>"><?php 
             } 
             print $row['label']; 
             if (!($row['type'] == "radio" || $row['type'] == "checkbox" )){?>
</label><?php } ?></td>
                <td><?php $this->_displayFormField($row['type'], $row['attributes']); ?></td>
              </tr>
<?php      
   } 
    ?>
              <tr>
                <td>&nbsp;</td>
                <td><?php 
              if ($this->readonly == true){
                $attributes['disabled'] = true;
              }else {
                $attributes['disabled'] = false;                
              }
              $this->_displayFormField('submit', $attributes); ?></td>
              </tr>
            </table>   
          </form>
      <?php
      }else if ($type == "fieldset"){
?>
      <form <?php ($this->id != NULL) ? print "id=\"$this->id\"" : print ""?> <?php ($this->enctype != NULL) ? print "enctype=\"$this->enctype\"" : print ""?> method="<?php print $this->method; ?>" action="<?php print $this->action; ?>">  
        <fieldset>
          <legend><?php print $this->title; ?></legend>
        <?php
   foreach ($this->fields as $row){
        if ($row['type'] != 'hidden'){?>
        <div>
          <div>
<?php 
             if (!($row['type'] == "radio" || $row['type'] == "checkbox" )){
          ?><label for="<?php print $row['attributes']['name']; ?>"><?php 
             } 
             print $row['label']; 
             if (!($row['type'] == "radio" || $row['type'] == "checkbox" )){
           ?></label><?php } ?> <br />
        <?php
           } $this->_displayFormField($row['type'], $row['attributes']);
        if ($row['type'] != 'hidden'){?>

</div></div>
<?php      
   }
   } 
    ?>

<?php $this->_displayFormField('submit'); ?>
        </fieldset>
      </form>
      <?php
            }else if ($type == "horizontal"){
?>
      <form <?php ($this->id != NULL) ? print "id=\"$this->id\"" : print ""?> <?php ($this->enctype != NULL) ? print "enctype=\"$this->enctype\"" : print ""?> method="<?php print $this->method; ?>" action="<?php print $this->action; ?>">  
      <div class="horizontal-form">

        <?php //print $this->title; ?>
        <?php
   foreach ($this->fields as $row){
        if ($row['type'] != 'hidden'){?>
            <label><?php print $row['label']; ?></label>
        <?php
           } $this->_displayFormField($row['type'], $row['attributes']);
        if ($row['type'] != 'hidden'){?>
<?php      
   }
   } 
    ?>

<?php $this->_displayFormField('submit'); ?>
            </div>
          </form>
<?php }?>
        </div>
<?php
    
  }


  public 
  function _displayFormField($type, $attributes = NULL){
  if (isset($attributes['comment'])){
    print("<div>".$attributes['comment']."</div>\n");
  }

  switch ($type){
  
  case "text":
    $this->_displayText($attributes);
    break;

  case "password":
    $this->_displayPassword($attributes);
    break;

  case "datetime":
    $this->_displayDatetime($attributes);
    break;

  case "date":
    $this->_displayDate($attributes);
    break;

  case "time":
    $this->_displayTime($attributes);
    break;

  case "hidden":
    $this->_displayHidden($attributes);
    break;

  case "file":
    $this->_displayFile($attributes);
    break;
    
  case "textarea":
    $this->_displayTextarea($attributes);
    break;
    
  case "select":
    $this->_displaySelect($attributes);
    break;
    
  case "radio":
    $this->_displayRadio($attributes);
    break;
      
  case "checkbox":
    $this->_displayCheckbox($attributes);    
    break;

	case "checkbox-list":
    $this->_displayCheckboxList($attributes);    
		break;

  case "comment":
    $this->_displayComment($attributes);    
    break;

  case "multilist":
    $this->_displayMultilist($attributes);    
    break;
    
  case "submit":
    $this->_displaySubmit($attributes);    
   	break;
    	
  case "cart_product":
    $this->_displayCartProduct($attributes);        	
    break;
  	}

		if (isset($attributes['help'])) {
			print("<span class=\"formHelp\" onclick=\"formFieldShowHelp(event, this, '$attributes[help]')\"> hjelp? </span>");
		}		
	}

	public
	function _displayText($attributes) {
    if (isset($attributes['readonly']) && $attributes['readonly'] == true){
    	print("<div id=\"$attributes[name]\">");
	    if (isset($attributes['value'])){
  	    print($attributes['value']);
    	}
    	print("</div>");
    	return;
    }
    print("<input type=\"text\"");
    if (isset($attributes['name'])){
      print(" name=\"$attributes[name]\"");
      print(" id=\"$attributes[name]\"");
    }
    if (isset($attributes['readonly']) && $attributes['readonly'] == true){
      print(" readonly=\"readonly\"");
      if (isset($attributes['class'])){
        $attributes['class'] .= " readonly";
      }else {
        $attributes['class'] = "readonly";        
      }
    }
    if (isset($attributes['class'])){
      print(" class=\"$attributes[class]\"");
    }
    if (isset($attributes['onchange'])){
      print(" onchange=\"$attributes[onchange]\"");
    }
    if (isset($attributes['onblur'])){
      print(" onblur=\"$attributes[onblur]\"");
    }
    if (isset($attributes['size'])){
      print(" size=\"$attributes[size]\"");
    }
    if (isset($attributes['maxlength'])){
      print(" maxlength=\"$attributes[maxlength]\"");
    }
    if (isset($attributes['value'])){
      print(" value=\"$attributes[value]\"");
    }
    print(" />");
    if (isset($attributes['tag'])){
      print("<input type=\"hidden\" name=\"tag\" value=\"$attributes[tag]\" />");
      print($attributes['tag']);
    }
    if (isset($attributes['repeatable'])){
      print("<a href=\"javascript: repeatElement('$attributes[name]'); \">ny link</a>");
    }
	}

	public
	function _displayPassword($attributes) {
		print("<input type=\"password\"");
    if (isset($attributes['name'])){
      print(" name=\"$attributes[name]\"");
      print(" id=\"$attributes[name]\"");
    }
    if (isset($attributes['class'])){
      print(" cols=\"$attributes[class]\"");
    }
    if (isset($attributes['size'])){
      print(" size=\"$attributes[size]\"");
    }
    if (isset($attributes['maxlength'])){
      print(" maxlength=\"$attributes[maxlength]\"");
    }
    print(" />");
		
	}
	
	public
	function _displayDatetime($attributes) {
		print("<input type=\"text\"");
    if (isset($attributes['name'])){
      print(" name=\"$attributes[name]\"");
      print(" id=\"$attributes[name]\"");
    }
    if (isset($attributes['readonly']) && $attributes['readonly'] == true){
      print(" readonly=\"readonly\"");
      if (isset($attributes['class'])){
        $attributes['class'] .= " readonly";
      }else {
        $attributes['class'] = "readonly";        
      }
    }
    if (isset($attributes['class'])){
      print(" cols=\"$attributes[class]\"");
    }
    print(" size=\"18\"");
    print(" maxlength=\"16\"");
    if (isset($attributes['value'])){
      print(" value=\"$attributes[value]\"");
    }else {
      print(" value=\"". date("Y-m-d H:i") ."\"");
    }
    
    print('onblur="validateDatetime(\'' . $attributes['name'] . '\');"'); 
    print(" /> (yyyy-mm-dd hh:mm)");
		
	}

	public
	function _displayDate($attributes) {
    print("<input type=\"text\"");
    if (isset($attributes['name'])){
      print(" name=\"$attributes[name]\"");
      print(" id=\"$attributes[name]\"");
    }
    if (isset($attributes['readonly']) && $attributes['readonly'] == true){
      print(" readonly=\"readonly\"");
      if (isset($attributes['class'])){
        $attributes['class'] .= " readonly";
      }else {
        $attributes['class'] = "readonly";        
      }
    }
    if (isset($attributes['class'])){
      print(" class=\"$attributes[class]\"");
    }
    print(" size=\"12\"");
    print(" maxlength=\"10\"");
    if (isset($attributes['value'])){
      print(" value=\"$attributes[value]\"");
    }else {
      print(" value=\"". date("Y-m-d") ."\"");
    }
    
    print('onblur="validateDate(\'' . $attributes['name'] . '\');"'); 
    print(" /> (yyyy-mm-dd)");		
	}

	public
	function _displayTime($attributes) {
    print("<input type=\"text\"");
    if (isset($attributes['name'])){
      print(" name=\"$attributes[name]\"");
      print(" id=\"$attributes[name]\"");
    }
    if (isset($attributes['readonly']) && $attributes['readonly'] == true){
      print(" readonly=\"readonly\"");
      if (isset($attributes['class'])){
        $attributes['class'] .= " readonly";
      }else {
        $attributes['class'] = "readonly";        
      }
    }
    if (isset($attributes['class'])){
      print(" cols=\"$attributes[class]\"");
    }
    print(" size=\"4\"");
    print(" maxlength=\"5\"");
    if (isset($attributes['value'])){
      print(" value=\"$attributes[value]\"");
    }else {
      print(" value=\"". date("H:i") ."\"");
    }
    
    print('onblur="validateTime(\'' . $attributes['name'] . '\');"'); 
    print(" /> (hh:mm)");
		
	}
	
	public
	function _displayHidden($attributes) {
    print("<input type=\"hidden\"");
    if (isset($attributes['name'])){
      print(" name=\"$attributes[name]\" id=\"$attributes[name]\"");
    }
    if (isset($attributes['value'])){
      print(" value=\"$attributes[value]\"");
    }
    print(" />");		
	}

	public
	function _displayRadio($attributes) {
    for ($i = 0; $i < count($attributes['values']); $i++){
      $value = $attributes['values'][$i];
      $label = $attributes['labels'][$i];
      if (isset($attributes['value']) && $attributes['value'] == $value){
        print("
                  <input type=\"radio\" name=\"$attributes[name]\" id=\"$value\" value=\"$value\" checked=\"checked\" /><label for=\"$value\">$label</label>\n");
      }else {
        print("
                  <input type=\"radio\" name=\"$attributes[name]\" id=\"$value\" value=\"$value\" /><label for=\"$value\">$label</label>\n");
      }
    }		
	}
	
	public
	function _displayCheckbox($attributes) {
    $name    = $attributes['name'];
    $label   = $attributes['label'];
    $checked = $attributes['checked'];
    if ($checked == true){
      print("<label for=\"$name\"><input type=\"checkbox\" name=\"$name\" id=\"$name\" checked=\"checked\" />$label</label><br />\n");
    }else {
      print("<label for=\"$name\"><input type=\"checkbox\" name=\"$name\" id=\"$name\" />$label</label><br />\n");
    }
	}

	public
	function _displayCheckboxList($attributes) {
		$list = $attributes['options'];
		$name_prefix = $attributes['name'];
		for ($i = 0; $i < sizeof($list['names']); $i++) {
	    $name    = $name_prefix.$list['names'][$i];
  	  $label   = $list['labels'][$i];
    	$checked = $list['checked'][$i];
	    if ($checked == true){
  	    print("<label for=\"$name\"><input type=\"checkbox\" name=\"$name\" id=\"$name\" checked=\"checked\" />$label</label><br />\n");
    	}else {
      	print("<label for=\"$name\"><input type=\"checkbox\" name=\"$name\" id=\"$name\" />$label</label><br />\n");
    	}
		}
	}

	public
	function _displayMultilist($attributes) {
    ?>
    <div id="multilist" class="multilist disabled">
      <div id="notice">

      </div>
      <ul id="finder">
<?php
    
    $currentDiv = NULL;
    while ($row =& $attributes['values']->fetchRow(DB_FETCHMODE_ORDERED)){
      if ($currentDiv != $row[0]){
        if ($currentDiv != NULL){
          print("</ul>\n");
          print("</li>\n");
        }
        $currentDiv = $row[0];
        print("<li>$row[1]\n");
        print("<ul>\n");
      }
      ?>
        <li><a href="javascript: makeRequest('pos.php?positionid=<?php print $row[2]; ?>');"><?php print $row[3]; ?></a></li>
  <?php
    }
    print("</ul>\n</li>\n");
?>

      </ul>
    </div>
    <?php    	
	}

	public
	function _displayComment($attributes) {
    print($attributes['value']);
	}

	public
	function _displaySelect($attributes) {
    print("<select name=\"$attributes[name]\" id=\"$attributes[name]\"");
    if (isset($attributes['disabled']) && $attributes['disabled'] == true){
      print(" disabled=\"$attributes[disabled]\"");
    }
    print(">");
    if (is_array($attributes['values'])){
      foreach ($attributes['values'] as $row){
        $value_id = $row['id'];
        $value    = $row['title'];
        if (isset($attributes['currentValue']) && $attributes['currentValue'] == $value_id){
          print("
                  <option value=\"$value_id\" selected=\"selected\">$value</option>");
        }else {
          print("
                  <option value=\"$value_id\">$value</option>");         
        }
      }
    }else if (is_a($attributes['values'], /* instanceof*/ "DB_Result")){//DB
      if (isset($attributes['nullPossible']) && $attributes['nullPossible'] == true){
        if ($attributes['currentValue'] == NULL){
          print("
                  <option value=\"\" selected=\"selected\">ingen</option>");
        }else {
          print("
                  <option value=\"\">ingen</option>");
        }

      }
      while($row =& $attributes['values']->fetchRow(DB_FETCHMODE_ORDERED)){
        $value_id = $row[0];
        $value    = $row[1];
        if (isset($attributes['currentValue']) && $attributes['currentValue'] == $value_id){
          print("
                  <option value=\"$value_id\" selected=\"selected\">$value</option>");
        }else {
          print("
                  <option value=\"$value_id\">$value</option>");         
        }
      }
    }
    print("</select>\n");
	}

	public
	function _displayFile($attributes) {
    print("<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"12000000\" />");
    print("<input type=\"file\"");

    if (isset($attributes['name'])){
      print(" name=\"$attributes[name]\"");
    }
    if (isset($attributes['size'])){
      print(" size=\"$attributes[size]\"");
    }
    print(" />");
	}

	public
	function _displayTextarea($attributes) {
    print("<textarea");
    if (isset($attributes['name'])){
      print(" name=\"$attributes[name]\"");
      print(" id=\"$attributes[name]\"");
    }
    if (isset($attributes['readonly']) && $attributes['readonly'] == true){
      print(" readonly=\"readonly\"");
      if (isset($attributes['class'])){
        $attributes['class'] .= " readonly";
      }else {
        $attributes['class'] = "readonly";        
      }
    }
    if (isset($attributes['class'])){
      print(" class=\"$attributes[class]\"");
    }
    if (isset($attributes['cols'])){
      print(" cols=\"$attributes[cols]\"");
    }
    if (isset($attributes['rows'])){
      print(" rows=\"$attributes[rows]\"");
    }
    //Uses javascript to validate length of textarea. Also displays current length
    if (isset($attributes['maxlength'])){
      print('onblur="validateSize(\'' . $attributes['name'] . '\', ' . $attributes['maxlength'] . ');"'); 
      print('onkeydown="textCounter(\'' . $attributes['name'] . '\', \'' . $attributes['name'] . 'Counter\', ' . $attributes['maxlength'].');"'); 
      print('onkeyup="textCounter(\'' . $attributes['name'] . '\', \'' . $attributes['name'] . 'Counter\', ' . $attributes['maxlength'].');"'); 
    }
    print(">");
    if (isset($attributes['value'])){
      print($attributes['value']);
    }
    print("</textarea>");
    if (isset($attributes['maxlength']) && 
        !isset($attributes['nocounter'])){?>
	    <input class="readonly counter" readonly="readonly" type="text" size="4" id="<?php print $attributes['name']; ?>Counter" value="<?php print $attributes['maxlength']; ?>" />
    <?php
    }     
	}

	public
	function _displaySubmit($attributes) {
   	print("<input type=\"submit\" value=\"Submit\" class=\"submit\"");
   	if ($attributes['disabled'] == true){
     	print(" disabled=\"disabled\"");
   	}
   	print(" />");	
	}

	public
	function _displayCartProduct($attributes) {	
  	$id_att = Array("name" => "product_id", "value" => $attributes['product_id']);
   	$id_type = "hidden";
   	$this->_displayFormField($id_type, $id_att);

   	$order_id_att = Array("name" => "order_id", "value" => $attributes['order_id']);
   	$order_type = "hidden";
   	$this->_displayFormField($order_type, $order_id_att);

   	$qty_type = "text";
   	$qty_att = Array("name" => "quantity", "size" => 1, "value" => 1);
   	print("legg ");
   	$this->_displayFormField($qty_type, $qty_att);
   	print(" stk i handlekurv<br />");
   	if ($attributes['allow_comment'] == 1) {
    	print("kommentar til denne bestillingen:<br />");
 	  	$comment_type = "textarea";
   		$comment_att = Array("name" => "comment", "rows" => 2, "cols" => 60);
   		$this->_displayFormField($comment_type, $comment_att);
    	print("<br />");
   	}
	}

}



?>
