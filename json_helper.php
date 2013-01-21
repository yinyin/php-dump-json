<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// --------

/**
 * Dump JSON from array
 *
 * For each array, an element with key '__json-type-hint' can be attached to hint structure type to
 * generator. The value of '__json-type-hint' can be 'array' or 'dict'.
 *
 * @access	public
 * @param	array	input array
 * @param	bool	directly sent result to STDOUT or not
 * @param	bool	call header to set Content-Type or not (only valid when to_stdout=true)
 * @return	string	resulted JSON or NULL when sent to STDOUT
 */
if ( !function_exists('dump_json') )
{
	function dump_json($obj, $to_stdout=false, $sendheader=false, $jsonp_callback=null)
	{
		if($to_stdout && $sendheader)
		{
			if(empty($jsonp_callback))
			{ header('Content-Type: application/json'); }
			else
			{ header('Content-Type: application/javascript'); }
		}

		if(!is_array($obj))
		{
			$result = null;

			if(is_int($obj) || is_float($obj))
			{ $result = $obj; }
			elseif(is_bool($obj))
			{ $result = ($obj ? 'true' : 'false'); }
			elseif(is_null($obj))
			{ $result = 'null'; }
			/* elseif(is_array($obj))   // no need to have this, just for reference
			{ return dump_json($obj, $to_stdout); } */
			else
			{ $result = '"'.addcslashes($obj, "\\\'\"&\n\r<>").'"'; }

			if(!empty($jsonp_callback))
			{ $result = $jsonp_callback.'('.$result.');'; }

			if($to_stdout)
			{ echo $result; return null; }

			return $result;
		}

		$typehint = null;
		if(isset($obj['__json-type-hint']))
		{
			$typehint = $obj['__json-type-hint'];
			unset($obj['__json-type-hint']);
		}

		$type = null;
		if(empty($typehint))
		{
			$type = 'array';
			$b = count($obj);
			$keys = array_keys($obj);
			foreach($keys as $k) {
				$kv = intval($k);

				if( ("${kv}" != $k) || ($kv >= $b) )
				{
					$type = 'dict';
					break;
				}
			}
		}
		else
		{ $type = $typehint; }

		// $r0 = ( ('array' == $type) ? '[' : '{' );   // $r0 = "${r0} /* ${debug} */ ";
		// $r9 = ( ('array' == $type) ? ']' : '}' );
		list($r0, $r9) = ('array' == $type) ? array('[', ']') : ( ('raw' == $type) ? array('', '') : array('{', '}') );

		if(!empty($jsonp_callback))
		{
			$r0 = $jsonp_callback.'('.$r0;
			$r9 = $r9.');';
		}

		if($to_stdout)
		{
			echo $r0;

			$c = '';
			if('array' == $type)
			{
				foreach($obj as $v) {
					echo $c;
					dump_json($v, true);
					$c = ',';
				}
			}
			elseif('raw' == $type)
			{
				$v = $obj['raw'];
				echo (empty($v) ? 'null' : $v);
			}
			else
			{
				foreach($obj as $k => $v) {
					echo $c, '"', addslashes($k), '":';
					dump_json($v, true);
					$c = ',';
				}
			}

			echo $r9;

			return null;
		}
		else
		{
			$compact = array();
			if('array' == $type)
			{
				foreach($obj as $v) {
					$compact[] = dump_json($v, false);
				}
			}
			elseif('raw' == $type)
			{
				$v = $obj['raw'];
				return empty($v) ? 'null' : $v;
			}
			else
			{
				foreach($obj as $k => $v) {
					$compact[] = '"'.addslashes($k).'":'.dump_json($v, false);
				}
			}

			return $r0.implode(',', $compact).$r9;
		}
	}
}



// vim: ts=4 sw=4 ai nowrap
