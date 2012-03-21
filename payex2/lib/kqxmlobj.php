<?php
/*

KqXmlobj - XML to object-parser

Copyright (c) 2004 Keyteq AS

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

If you have any questions or comments, please email:

Jon Helge Stensrud
support.solutions@payex.no

Keyteq AS
http://www.keyteq.no

*/

class KQXmlobj
{
	var $name; // xml tag-name
	var $error; // error occurred
	var $attributes; // xml-attributes (<name attribute="value")
	var $lastchild; // arrayposition of last child returned in get_child()
	var $lastname; // tag-name of last child returned in get_child()
	var $errormessage;
	var $values; // array of objects or value of tag

	function KQXmlobj($name = '')
	{
		$this->values = null;
		$this->error = false;
		$this->attributes = array();
		$this->name = $name;
		$this->cdata = false;
		$this->lastchild = -1;
		$this->lastname = '';
		$this->errormessage = '';
	}

	/*
		Get attribute-value

		$test->from_xml('<test id="55" />');
		echo $test->get_attribute('id');
	
		Outputs:
		55
	*/
	function get_attribute($attribute)
	{
		if (isset($this->attributes[$attribute]))
			return $this->attributes[$attribute];
		else
			return false;
	}

	/*
		Get value from tag

		$test->from_xml('<test>value</test>');
		echo $test->get_value();

		Outputs:
		value

		$person->from_xml('<person><firstname>Jon</firstname></person>');
		echo $person->get_value('firstname');

		Outputs:
		Jon
	*/
	function get_value($name = '')
	{
		if ($name == '')
		{
			if (!is_object($this->values[0]))
				return $this->values[0];
		}
		else
		{
			if (is_array($this->values))
			{
				for ($i=0, $valuescount = count($this->values); $i < $valuescount; $i++)
				{
					if ( is_object($this->values[$i]) && $this->values[$i]->name == $name && !is_object($this->values[$i]->values[0]))
					{
						return $this->values[$i]->values[0];
					}
				}
			}
		}
		return null;
	}

	/*
		Get tag name
		$xml->from_xml('<root />')
		echo $xml->get_name();

		Outputs:
		root
	*/
	function get_name()
	{
		return $this->name;
	}

	/*
		Get child tag

		$xml->from_xml('<root><test /><nottest /><test /></root>');
		while ( $test = &$xml->get_child('test') )
		{
			echo 'test';
		}

		Outputs:
		testtest

		$xml->from_xml('<root><test /><test2 /></root>');
		while ( $test = &$xml->get_child() )
		{
			echo $test->get_name();
		}

		Outputs:
		testtest2
	*/
	function& get_child($name = '')
	{
		if (is_array($this->values)) 
		{
			if ( $this->lastname != $name )
			{
				$this->lastchild = -1;
				$this->lastname = $name;
			}

			$valuescount = count($this->values);

			do
			{
				$this->lastchild++;
				if (isset($this->values[$this->lastchild]) && is_object($this->values[$this->lastchild]) && ($this->values[$this->lastchild]->name == $name xor $name == ''))
					return ( $this->values[$this->lastchild] );
			}
			while ($this->lastchild < $valuescount - 1);
		}
		$null = null;
		return $null;
	}

	/*
		Get first child tag with a given attribute/value-combination

		$test->from_xml('<test><element name="firstname">Jon Helge</element><element name="lastname">Stensrud</element></test>');

		$ln =& $test->get_child_byattribute('element', 'name', 'lastname');
		echo $ln->get_value();

		Outputs:
		Stensrud
	*/
	function& get_child_byattribute($tag, $attribute, $value)
	{
		for ( $i=0,$c=count($this->values); $i<$c; $i++ )
		{
			if ( is_object($this->values[$i]) )
			{
				if ( $this->values[$i]->get_name() == $tag ) 
				{
					$v = $this->values[$i]->get_attribute($attribute);
					if ( $v == $value )
					{
						return $this->values[$i];
					}
				}
			}
		}
		return $null = null;
	}

	/*

		Reset child-pointer, making get_child() start from top

	*/
	function reset_child()
	{
		$this->lastchild = -1;
		$this->lastname = null;
	}

	/*
		
		Get CDATA

	*/
	function get_cdata()
	{
		for ( $i=0, $c=count($this->values); $i<$c; $i++)
		{
			if ( is_object($this->values[$i]) )
			{
				if ( $this->values[$i]->is_cdata() )
				{
					return $this->values[$i]->get_value();
				}
			}
		}
		return '';
	}

	/*

		Check if a tag is a CDATA-tag.

	*/
	function is_cdata()
	{
		return $this->cdata;
	}

	/*

		Check for errors (from parsing)

	*/
	function get_error()
	{
		return $this->error;
	}

	/*

		Get errormessage (from parsing)

	*/
	function get_errormessage()
	{
		return $this->errormessage;
	}

	/*

		Get the number of values/tags in a tag

	*/
	function values_count()
	{
		if (!is_array($this->values))
			return 0;
		else
			return count($this->values);
	}

	/*

		Parse XML into object-structure

		$xml = &new KQXmlobj();
		$xml->from_xml('<xml><tag /></xml>');
		echo $xml->get_name().';';
		$tag = &$xml->get_child('tag');
		echo $tag->get_name();

		Outputs:
		xml;tag
	*/
	function from_xml($xml, $strip = false)
	{
		if ( $strip === true )
			$xml = stripcslashes($xml);

		$enc = 'iso-8859-1';
		$startpos = strpos($xml, '<?xml');
		if ( $startpos !== false )
		{
			if ( ( ($encstart = strpos($xml, 'encoding', $startpos+1)) !== false )
				&& ( ($encquotestart = strpos($xml, '"', $encstart+1)) !== false )
				&& ( ($encquoteend = strpos($xml, '"', $encquotestart+1)) !== false ) )
					$enc = substr($xml, $encquotestart+1, $encquoteend-$encquotestart-1);
		}
		$enc = strtoupper($enc);
		switch ( $enc )
		{
			case 'ISO-8859-1':
			case 'US-ASCII':
			case 'UTF-8': break;
			default:
				$this->error = true;
				$this->errormessage = 'Unsupported encoding, '.$enc;
				return '';
				break;
		}

		$parser = xml_parser_create($enc);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'iso-8859-1');
		xml_parse_into_struct($parser, $xml, $vals, $index);
		$errcode = xml_get_error_code($parser);
		xml_parser_free($parser);

		if ( $errcode > 0 )
		{
			switch ( $errcode )
			{
				case XML_ERROR_NO_MEMORY:					$this->error = true; $this->errormessage = 'No memory'; break;
				case XML_ERROR_SYNTAX:						$this->error = true; $this->errormessage = 'Syntax error'; break;
				case XML_ERROR_NO_ELEMENTS:					$this->error = true; $this->errormessage = 'No elements'; break;
				case XML_ERROR_INVALID_TOKEN:				$this->error = true; $this->errormessage = 'Invalid token'; break;
				case XML_ERROR_UNCLOSED_TOKEN:				$this->error = true; $this->errormessage = 'Unclosed token'; break;
				case XML_ERROR_PARTIAL_CHAR:				$this->error = true; $this->errormessage = 'Partial char'; break;
				case XML_ERROR_TAG_MISMATCH:				$this->error = true; $this->errormessage = 'Tag mismatch'; break;
				case XML_ERROR_DUPLICATE_ATTRIBUTE:			$this->error = true; $this->errormessage = 'Duplicate attribute'; break;
				case XML_ERROR_JUNK_AFTER_DOC_ELEMENT:		$this->error = true; $this->errormessage = 'Junk after DOC element'; break;
				case XML_ERROR_PARAM_ENTITY_REF:			$this->error = true; $this->errormessage = 'Param entity ref'; break;
				case XML_ERROR_UNDEFINED_ENTITY:			$this->error = true; $this->errormessage = 'Undefined entity'; break;
				case XML_ERROR_RECURSIVE_ENTITY_REF:		$this->error = true; $this->errormessage = 'Recursive entity ref'; break;
				case XML_ERROR_ASYNC_ENTITY:				$this->error = true; $this->errormessage = 'Async entity'; break;
				case XML_ERROR_BAD_CHAR_REF:				$this->error = true; $this->errormessage = 'Bad char ref'; break;
				case XML_ERROR_BINARY_ENTITY_REF:			$this->error = true; $this->errormessage = 'Binary entity ref'; break;
				case XML_ERROR_ATTRIBUTE_EXTERNAL_ENTITY_REF: $this->error = true; $this->errormessage = 'Attribute external entity ref'; break;
				case XML_ERROR_MISPLACED_XML_PI:			$this->error = true; $this->errormessage = 'Misplaced xml pi'; break;
				case XML_ERROR_UNKNOWN_ENCODING:			$this->error = true; $this->errormessage = 'Unknown encoding'; break;
				case XML_ERROR_INCORRECT_ENCODING:			$this->error = true; $this->errormessage = 'Incorrect encoding'; break;
				case XML_ERROR_UNCLOSED_CDATA_SECTION:		$this->error = true; $this->errormessage = 'Unclosed cdata section'; break;
				case XML_ERROR_EXTERNAL_ENTITY_HANDLING:	$this->error = true; $this->errormessage = 'External entity handling'; break;
			}
			return '';
		}

		$stack = array();
		$stack[] = &$this;
		$stackcount = 1;

		for ( $i=0, $c=count($vals); $i<$c; $i++)
		{
			switch ( $type = $vals[$i]['type'] )
			{
				case 'open':
				case 'complete':
					if ( $i == 0 )
					{
						$obj = &$this;
						$this->name = $vals[$i]['tag'];
					}
					else
					{
						$obj = &$stack[$stackcount-1]->__insert($vals[$i]['tag']);
						if ( $type == 'open')
							$stack[$stackcount++] = &$obj;
					}
					if ( isset($vals[$i]['attributes']) )
					{
						foreach ( $vals[$i]['attributes'] as $key => $value )
							$obj->attributes[$key] = $value;
					}
					if ( isset($vals[$i]['value']) )
						$obj->__text($vals[$i]['value']);
					break;
				case 'cdata':
					$stack[$stackcount-1]->__insert_cdata($vals[$i]['value']);
					break;
				case 'close':
					array_pop($stack);
					$stackcount--;
					break;
			}
		}
	}

	function __text($text)
	{
		if (!is_array($this->values))
		{
			$this->values = array();
		}
		$this->values[] = (string) $text;
	}
	function& __insert($name, $value = null)
	{
		if (!is_array($this->values))
		{
			$this->values = array();
		}
		if ( is_object($name) )
		{
			$obj = &$this->__obj_insert($name);
			return $obj;
		}
		else
		{
			$new = &new KQXmlobj();
			$new->name = $name;
			
			if ($value !== null && $value !== false )
				$new->values[0] = (string) $value;
			
			$this->values[] = &$new;
			return $new;
		}
	}
	function& __obj_insert(&$obj)
	{
		if (!is_array($this->values))
		{
			$this->values = array();
		}
		$valuescount = count($this->values);
		$this->values[$valuescount] = &$obj;
		return $this->values[$valuescount];
	}
	function __insert_cdata($text)
	{
		if (!is_array($this->values))
			$this->values = array();

		$new = &new KQXmlobj();
		$new->name = '[CDATA]';
		$new->values[0] = $text;
		$new->cdata = true;
		$this->values[] = &$new;
	}
}
?>