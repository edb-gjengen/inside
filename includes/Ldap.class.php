<?php

class Ldap {

	private $conn = null;
	
	public function __construct($host, $port = 389) {
		$this->conn = ldap_connect($host, $port);
		if( $this->conn == null ) {
			throw new Exception("Could not connect to ldap server `$host:$port'");
		}
		ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
	}


	public function __destruct() {
		ldap_close($this->conn);
	}

	public function getConn() {
		return $this->conn;
	}

	private function bind() {
		if( !ldap_bind($this->conn, BIND_USER, BIND_PASS) ) {
			throw new Exception("ERROR: Could not bind to ldap server.");
		}
	}

	private function unbind() {
		/* Seems the ldap_unbind() function closes the connection,
		 * so we just drop the unbind process. Some security risk
		 * as the script continues bound to the already bound user,
		 * but what can one do?... */
	}
    public function testBind($username, $password) {
        $password = strtr($password, array("\'"=>"'"));
        $baseDn = USUFFIX . "," . SUFFIX;
        if ( ($isBound = @ldap_bind($this->conn, $username, $password)) === false ) {
            // @see wpLDAP comment at http://ashay.org/?page_id=133#comment-558
            $isBound = @ldap_bind($this->conn, "uid=$username, $baseDn", $password);
        }
        return $isBound;
    }

	public function search($baseDn, $filter, $attrs, $bind = false) {
		if( $bind === true ) {
			$this->bind();
		}
		$res = ldap_search($this->conn, $baseDn, $filter, $attrs);
		if(!$res) {
			return false;
		}

		$entries = ldap_get_entries($this->conn, $res);

		if( $bind === true ) {
			$this->unbind();
		}

		return $entries;
	}

	public function read($dn, $filter, $attrs, $bind = false) {
		if( $bind === true ) {
			$this->bind();
		}
		$res = ldap_read($this->conn, $dn, $filter, $attrs);
		if(!$res) {
			return false;
		}

		$entries = ldap_get_entries($this->conn, $res);

		if( $bind === true ) {
			$this->unbind();
		}

		return $entries;
	}

	private function userDn($username) {
		return "uid=". $username . "," . USUFFIX . "," . SUFFIX;
	}

	private function groupDn($groupname) {
		return "cn=". $groupname . "," . GSUFFIX . "," . SUFFIX;
	}
	
	private function homeAutomountDn($username) {
		return "cn=". $username . ",ou=auto.home," . ASUFFIX . "," . SUFFIX;
	}

	private function kerberosPrincipalDn($username) {
		return "krbPrincipalName=". $username . "@". KERBEROS_DOMAIN .",cn=". KERBEROS_DOMAIN . ","
			. KSUFFIX . "," . SUFFIX;
	}

	private function ldapAdd($dn, $record) {
		$this->bind();
		$res = ldap_add($this->conn, $dn, $record);
/*		if ( !$res ) {
			_log(ldap_error($this->conn));
		}*/
		$this->unbind();

		if( $res ) {
			debug("Added $dn");
		} else {
			debug("Could not add $dn");
		}

		return $res;
	}

	public function ldapDelete($dn) {
		$this->bind();
		$res = ldap_delete($this->conn, $dn);
		$this->unbind();

		if( $res ) {
			debug("Deleted $dn");
		} else {
			debug("Could not delete $dn");
		}
		return $res;
	}

	public function getUser($username) {

		$filter = "(&(uid=$username)(objectClass=posixAccount))";
		$attrs = array();
		
		$info = $this->search(USUFFIX .",". SUFFIX, $filter, $attrs);

		if( $info['count'] != 1 ) {
			throw new Exception("Error retreiving user " . $username . ".");
		}

		$fl = $this->flatten($info);

		if( count($fl) != 1 ) {
			throw new Exception("Unexpected size of user array.");
		}

		/* Removing objectclass... */
		unset($fl[0]['objectclass']);

		return $fl[0];

	}
	public function getUserWithMail($mail) {

		$filter = "(&(mail=$mail)(objectClass=posixAccount))";
		$attrs = array();
		
		$info = $this->search(USUFFIX .",". SUFFIX, $filter, $attrs);

		if( $info['count'] != 1 ) {
			throw new Exception("Error retreiving user " . $username . ".");
		}

		$fl = $this->flatten($info);

		if( count($fl) != 1 ) {
			throw new Exception("Unexpected size of user array.");
		}

		/* Removing objectclass... */
		unset($fl[0]['objectclass']);

		return $fl[0];

	}

	private function flatten($info) {
		$res = array();

		for($i=0;$i<$info['count'];$i++) {
			$obj_i = $info[$i];
			for($j=0;$j<$obj_i['count'];$j++) {
				if( $obj_i[$obj_i[$j]]['count'] == 1) {
					$res[$i][$obj_i[$j]] = $obj_i[$obj_i[$j]][0];
				} else {
					for($k=0;$k<$obj_i[$obj_i[$j]]['count'];$k++) {
						$res[$i][$obj_i[$j]][] = $obj_i[$obj_i[$j]][$k];
					}
				}
			}
		}
		return $res;
	}

	public function getAllUsers() {
		$filter = "(uid=*)";
		$attrs = array();
		$res = ldap_search($this->conn, USUFFIX . "," . SUFFIX, $filter, $attrs);
		if(! $res ) {
			throw new Exception("Could not search ldap directory for users.");
		}

		$users = array();

		$info = ldap_get_entries($this->conn, $res);
		for($i=0;$i<$info['count'];$i++) {
			$users[] = $info[$i];
		}
		return $users;
	}

	public function getAllUsernames() {
		$filter = "(&(uid=*))";
		$attrs = array("uid");
		$res = $this->search(USUFFIX .",". SUFFIX, $filter, $attrs);
		if(!$res) {
			throw new Exception("Could not search ldap directory for users.");
		}

		$users = array();
		for($i=0;$i<$res['count'];$i++) {
			$users[] = $res[$i]['uid'][0];
		}
		return $users;
	}

	public function getAllPrivateGroups() {
		$filter = "(&(!(cn=dns-*))(objectclass=posixGroup))";
		$attrs = array();
		$res = $this->search(GSUFFIX . "," . SUFFIX, $filter, $attrs);
		if(!$res) {
			throw new Exception("Could not search ldap directory for private groups.");
		}

		$groups = array();

		for($i=0;$i<$res['count'];$i++) {
			$group = array();
			$group['cn'] = $res[$i]['cn'][0];
			$group['description'] = $res[$i]['description'][0];
			$group['gidNumber'] = (int) $res[$i]['gidnumber'][0];
			$groups[] = $group;
		}


		return $groups;
	}

	public function getAllDnsGroups() {
		$filter = "(&(cn=dns-*)(objectclass=posixGroup))";
		$attrs = array();
		$res = $this->search(GSUFFIX . "," . SUFFIX, $filter, $attrs);
		if(!$res) {
			throw new Exception("Could not search ldap directory for dns groups.");
		}

		$groups = array();

		for($i=0;$i<$res['count'];$i++) {
			$group = array();
			$group['cn'] = $res[$i]['cn'][0];
			$group['description'] = $res[$i]['description'][0];
			$group['gidNumber'] = (int) $res[$i]['gidnumber'][0];
			$groups[] = $group;
		}

		return $groups;
	}

	public function getGroupMembers($group) {
		$filter = "(&(cn=".$group.")(objectclass=posixGroup))";
		$attrs = array("memberUid");
		$res = $this->read($this->groupDn($group), $filter, $attrs);
		
		$members = array();
		
		if( ! $res ) {
			return $members;
		}
		/* Return an empty array for the groups that do not
		* contain any members */
		if( $res[0]['count'] == 0 ) return $members;

		for($i=0;$i<$res[0]['memberuid']['count'];$i++) {
			$members[] = $res[0]['memberuid'][$i];
		}

		return $members;
	}

	public function getUserGroups($username) {
		$filter = "(&(memberUid=$username))";
		$attrs = array("cn");
		$attrsonly = 0;
		$res = ldap_search($this->conn, GSUFFIX . "," . SUFFIX,
			$filter, $attrs, $attrsonly);
		if( !$res ) {
			throw new Exception("ERROR. An error occured while searching for usergroups.");
		}
		$info = ldap_get_entries($this->conn, $res);

		$groups = array();
		for($i = 0; $i < $info['count']; $i++) {
			array_push($groups, $info[$i]['cn'][0]);
		}

		return $groups;
	}

	public function userExists($username) {
		if( empty($username) ) {
			throw new Exception("<". __FUNCTION__  ."> ERROR: username is empty or null.");
		}
		$dn = $this->userDn($username);
		$filter = "(&(uid=$username))";
		$attrs = array("dn");
		$attrsonly = 1;
		
		$res = ldap_search($this->conn, USUFFIX . "," . SUFFIX,
			$filter, $attrs, $attrsonly);

		if( !$res ) {
			throw new Exception("ERROR: An error occured while searching for user.");
		}
		
		$info = ldap_get_entries($this->conn, $res);

		return ($info['count'] == 0? false : true);
	}
	public function mailExists($mail) {
		if( empty($mail) ) {
			throw new Exception("<". __FUNCTION__  ."> ERROR: mail is empty or null.");
		}
                $dn = USUFFIX . "," . SUFFIX;
		$filter = "(mail=$mail)";
		$attrs = array("mail");
		$attrsonly = 1;
		
		$res = ldap_search($this->conn, USUFFIX . "," . SUFFIX,
			$filter, $attrs, $attrsonly);

		if( !$res ) {
			throw new Exception("ERROR: An error occured while searching for user.");
		}
		
		$info = ldap_get_entries($this->conn, $res);

		return ($info['count'] == 0? false : true);
	}

	public function groupExists($groupname) {
		if( empty($groupname) ) {
			throw new Exception("<". __FUNCTION__  ."> ERROR: groupname is empty or null.");
		}
		$filter = "(objectclass=*)";
		$attrs = array("cn");
		$res = ldap_read($this->conn, $this->groupDn($groupname), $filter, $attrs);

		if( !$res ) {
			return false;
//			throw new Exception("<". __FUNCTION__ ."> ERROR: could not read group");
		}

		$count = ldap_count_entries($this->conn, $res);

		return ($count == 1? true : false);
	}

	public function homeDirectoryMountExists($username) {
		if( empty($username) ) {
			throw new Exception("<". __FUNCTION__  ."> ERROR: username is empty or null.");
		}
		$filter = "(objectclass=*)";
		$attrs = array("cn");
		$res = ldap_read($this->conn, $this->homeAutomountDn($username), $filter, $attrs);

		if( !$res ) {
			return false;
//			throw new Exception("<". __FUNCTION__ ."> ERROR: could not read automount");
		}

		$count = ldap_count_entries($this->conn, $res);

		return ($count == 1? true : false);
	
	}
	private function ldapError() {
		return ldap_error($this->conn);
	}

	public function kerberosPrincipalExists($username) {
		if( empty($username) ) {
			throw new Exception("<". __FUNCTION__  ."> ERROR: username is empty or null.");
		}

		$filter = "(objectclass=*)";
		$attrs = array("krbprincipalname");
		$res = ldap_read($this->conn, $this->kerberosPrincipalDn($username), $filter, $attrs);

		if( !$res ) {
			return false;
		}

		$count = ldap_count_entries($this->conn, $res);

		return ($count == 1? true : false);
	
	}

	private function getNextGid() {
		$dn = GSUFFIX . "," . SUFFIX;
		$filter = "(&(cn=dns-*))";
		$attrs = array("gidNumber");
		$res = ldap_search($this->conn, $dn, $filter, $attrs);

		if( !$res ) {
			throw new Exception("ERROR: could not perform search for gid.");
		}

		$info = ldap_get_entries($this->conn, $res);

		$num = (int) GID_MIN;		

		for($i = 0; $i < $info['count']; $i++) {
			if( $num < $info[$i]['gidnumber'][0] ) {
				$num = (int) $info[$i]['gidnumber'][0];
			}
		}

		if( $num >= (int) GID_MAX ) {
			throw new Exception("ERROR: Gid number larger then GID_MAX");
		}
		
		return $num + 1;
	}

	private function getNextUserGid() {
		$dn = GSUFFIX . "," . SUFFIX;
		$filter = "(&(!(cn=dns-*))(gidNumber=*))";
		$attrs = array("gidNumber");
		$res = ldap_search($this->conn, $dn, $filter, $attrs);

		if( !$res ) {
			throw new Exception("ERROR: could not perform search for gid.");
		}

		$info = ldap_get_entries($this->conn, $res);

		$num = (int) USER_GID_MIN;
		for($i = 0; $i < $info['count']; $i++) {
			if( $num < $info[$i]['gidnumber'][0] ) {
				$num = (int) $info[$i]['gidnumber'][0];
			}
		}

		if( $num >= (int) USER_GID_MAX ) {
			throw new Exception("Error: Gid number larger then USER_GID_MAX");
		}

		return $num + 1;
	}

	private function getNextUid() {
		$dn = USUFFIX . "," . SUFFIX;
		$filter = "(&(uid=*))";
		$attrs = array("uidNumber");
		$res = ldap_search($this->conn, $dn, $filter, $attrs);
		if( !$res ) {
			throw new Exception("ERROR: could not perform search for uid.");
		}
		$info = ldap_get_entries($this->conn, $res);
		$num = (int) UID_MIN;
		for($i = 0; $i < $info['count']; $i++) {
			if( $num < $info[$i]['uidnumber'][0] ) {
				$num = (int) $info[$i]['uidnumber'][0];
			}
		}

		if( $num >= (int) UID_MAX ) {
			throw new Exception("ERROR: UID number larger then UID_MAX");
		}

		return $num + 1;
	}

	private function createUserHomeDirectory($user) {
		$username = $user['username'];
		$res = exec("sudo -u localedb /opt/edb/inside_ldap/create_user_home_directory.sh $username",
			$output, $retval);
		if( $retval != 0 ) {
			throw new Exception("Could not create users home directory: $res");
		}
		debug("Created home directory");
		return true;
	}

	private function createUserHomeDirectoryMount($username) {
		$info = array();
		
		$info['objectClass'] = array("top", "automount");

		$info['cn'] = $username;
		$info['automountInformation'] = "-fstype=nfs4,rw,sec=krb5 " . FILESERVER . ":" . FILESERVER_HOMES .
			"/" . $username;
		
		$dn = "cn=" . $username .",ou=auto.home,ou=Automount," . SUFFIX;

		return $this->ldapAdd($dn, $info);
	}

	private function deleteUserHomeDirectoryMount($username) {
		
		$dn = "cn=" . $username .",ou=auto.home,ou=Automount," . SUFFIX;

		return $this->ldapDelete($dn);
	}

	private function createUserKerberosCredentials($username, $password) {
		$str = "sudo -u localedb /opt/edb/inside_ldap/create_user_kerberos_credentials.sh $username $password";
		$res = exec($str, $output, $retval);
		if( $retval != 0 ) {
			ldap_log("ERROR creating kerberos credentials: " . implode(",", $output));
			throw new Exception("Could not create users kerberos credentials");
		}
		debug("Created users kerberos credentials");
		return true;
	}

	private function deleteUserKerberosCredentials($username) {
		$dn = "krbPrincipalName=$username@NEUF.NO,cn=NEUF.NO,cn=krbcontainer," . SUFFIX;
		return $this->ldapDelete($dn);
	}

	public function addGroup($name, $description, $gid = null) {
		$dn = $this->groupDn($name);

		$info = array();
		$info['objectClass'] = array();
		$info['objectClass'][0] = "posixGroup";
		$info['description'] = $description;
		$info['gidNumber'] = (int) ($gid == null? $this->getNextGid() : $gid);

		return $this->ldapAdd($dn, $info);
	}


	public function addUser($userinfo) {
		if( !is_array($userinfo) ) {
			throw new Exception("ERROR: Userinfo is not an array");
		}

		$info = array();
		/* Objectclass */
		$info['objectClass'] = array();
		$info['objectClass'][0] = "inetOrgPerson";
		$info['objectClass'][1] = "posixAccount";
		$info['objectClass'][2] = "shadowAccount"; 
		
		/* User dn (identifies this record in LDAP) */
		$dn = $this->userDn($userinfo['username']);
		
		/* General user info */
		$info['uid'] = $userinfo['username'];
		$info['givenname'] = $userinfo['firstname'];
		$info['sn'] = $userinfo['lastname'];
		$info['cn'] = $userinfo['firstname'] . " " . $userinfo['lastname'];
		$info['displayname'] = $userinfo['firstname'] . " " . $userinfo['lastname'];
		$info['gecos'] = $this->gecos($userinfo['username']);
		$info['mail'] = $userinfo['email'];
		
		/* System specific user info */
		$info['userpassword'] = "{SSHA}". ssha($userinfo['password']);
		$info['loginshell'] = LOGIN_SHELL;
		$info['shadowexpire'] = (int) SHADOW_EXPIRE;
		$info['shadowflag'] = (int) SHADOW_FLAG;
		$info['shadowwarning'] = (int) SHADOW_WARNING;
		$info['shadowmin'] = (int) SHADOW_MIN;
		$info['shadowmax'] = (int) SHADOW_MAX;
		$info['shadowlastchange'] = (int) SHADOW_LAST_CHANGE;
		$info['homedirectory'] = HOME_DIRECTORY_PREFIX . "/" . $userinfo['username'];
		$info['uidnumber'] = (int) $this->getNextUid();
		$info['gidnumber'] = (int) $this->getNextUserGid();

		/* Check if the user is already in the LDAP directory */
		$user = $this->userExists($userinfo['username']);
		if( $user == true ) {
			throw new Exception("User ". $userinfo['username'] . " already exists.");
		}
		
		if( $this->addGroup($userinfo['username'], "Usergroup", $info['gidnumber']) 
			=== false ) {
			throw new Exception("ERROR: ldap_add `group' failed.");
		}

		if( $this->ldapAdd($dn, $info) === false ) {
			throw new Exception("ERROR: ldap_add user: `". $info['uid']."' failed.");
		}

		if( $this->createUserHomeDirectory($userinfo) === false ) {
			throw new Exception("ERROR: could not create user home directory.");
		}

		if( $this->createUserHomeDirectoryMount($userinfo['username']) === false ) {
			throw new Exception("ERROR: could not create home directory mount in ldap");
		}

		if( $this->createUserKerberosCredentials($userinfo['username'], $userinfo['password']) === false) {
			throw new Exception("ERROR: could not create user kerberos credentials.");
		}
		return true;
	}

	public function addUserToGroup($username, $groupname) {
		$dn = $this->groupDn($groupname);
		$attr = array();
		$attr['memberUid'][] = $username;
		
		$this->bind();
		$res = ldap_mod_add($this->conn, $dn, $attr);
		$this->unbind();
		
		if( $res ) {
			debug("Added $username to $dn");
		} else {
			debug("Could not add $username to $dn");
		}

		return $res;
	}

	public function removeUserFromGroup($username, $groupname) {
		$dn = $this->groupDn($groupname);
		$attr = array();
		$attr['memberUid'][] = $username;
		
		$this->bind();
		$res = ldap_mod_del($this->conn, $dn, $attr);
		$this->unbind();
		
		if( $res ) {
			debug("Removed $username from $dn");
		} else {
			debug("Could not remove $username from $dn");
		}

		return $res;
	}

	public function deleteUser($username) {
		/* Find all groups the user is a member of */
		$usergroups = $this->getUserGroups($username);
	
		foreach($usergroups as $group) {
			$this->removeUserFromGroup($username, $group);
		}

		$this->deleteUserHomeDirectoryMount($username);
		$this->deleteUserKerberosCredentials($username);

		/* Delete the users usergroup */
		$this->ldapDelete($this->groupDn($username));

		/* Delete the user */
		$this->ldapDelete($this->userDn($username));

		return true;
	}

	public function setUserPassword($username, $password) {
		$dn = $this->userDn($username);
		$this->bind();
		$attrs = array();
		$attrs['userPassword'] = ssha($password);
		$res = ldap_mod_replace($this->conn, $dn, $attrs);

		if( $res ) {
			debug("Password set/change for $dn");
		} else {
			debug("Could not set password for $dn");
		}

		return $res;
	}

	private function gecos($string) {
		return $string;
	}

	public function printIntegrityCheckStart($name) {
//		debug("Running integrity check <" . $name . ">");
		_log_check("Running integrity check <" . $name . ">");
	}

	public function printIntegrityCheckStop($name) {
//		debug("Completed integrity check <" . $name . ">");
		_log_check("Completed integrity check <" . $name . ">");
	}

	public function runAllIntegrityChecks() {
		_log_check("------ Starting all integrity checks ------");
		$pass = true;
		$methods = get_class_methods(__CLASS__);
		foreach($methods as $method) {
			if( strpos($method, "check") === 0 ) {
				$this->printIntegrityCheckStart($method);
				try {
					if( $this->$method() === false ) {
						$pass = false;
					}
					
				} catch( Exception $e ) {
					_log_check($e->getMessage());
				}
				$this->printIntegrityCheckStop($method);
			}
		}

		if( $pass === false ) {
			mail_admins("Integrity check failed, please review log.");
		}
		
		_log_check("------ Completed all integrity checks (pass=". ($pass? "true":"false") .") ------");
	}

	public function checkUserPrimaryGroup() {
		$filter = "(&(uid=*))";
		$attrs = array("uid");
		$res = $this->search(USUFFIX .",". SUFFIX, $filter, $attrs, true);
		if(!$res) {
			return false;
		}

		$return_val = true;

		for($i=0;$i<$res['count'];$i++) {
			$user = $res[$i];
			if( $this->groupExists($user['uid'][0]) === false ) {
				_log_check_error("User `". $user['uid'][0] ."' does not have a primary group.");
				$return_val = false;
			}
		}

		return $return_val;
	}

	public function checkUserKerberosPrincipal() {
		$users = $this->getAllUsernames();
		$return_val = true;
		foreach($users as $user) {
			if( $this->kerberosPrincipalExists($user) === false ) {
				_log_check_error("User `". $user . "' does not have a kerberos principal.");
				$return_val = false;
			}
		}

		return $return_val;
	}

	public function checkUserAutomountHomeDirectory() {
		$users = $this->getAllUsernames();
		$return_val = true;
		foreach($users as $user) {
			if( $this->homeDirectoryMountExists($user) === false ) {
				_log_check_error("User `". $user . "' does not have a home directory mount record.");
				$return_val = false;
			}
		}

		return $return_val;
	}

	public function checkPrivateGroupUserExists() {
		$private_groups = $this->getAllPrivateGroups();
		$return_val = true;
		foreach($private_groups as $group) {
			/* Special groups that don't require a user */
			if( $group['cn'] == "edb" ) continue;
			if( $group['cn'] == "edbadmin" ) continue;

			if( $this->userExists($group['cn']) === false ) {
				_log_check_error("Private Group `" . $group['cn'] . "' exists but user does not.");
				$return_val = false;
			}
		}

		return $return_val;
	}

	public function checkDnsGroupMembersExists() {
		$dns_groups = $this->getAllDnsGroups();

		$return_val = true;

		foreach($dns_groups as $group) {
//			debug("Getting members for " . $group['cn']);
			$members = $this->getGroupMembers($group['cn']);
			foreach($members as $member) {
				if( !$this->userExists($member) ) {
					_log_check_error("Group `". $group['cn'] ."' contain user `". $member .
						"', but the user does not exist.");
					$return_val = false;
				}
			}
		}

		/* These two are special groups */
		foreach(array("edb", "edbadmin") as $group) {
			$members = $this->getGroupMembers($group);
			foreach($members as $member) {
				if( !$this->userExists($member) ) {
					_log_check_error("Group `". $group ."' contain user `". $member .
						"', but the user does not exist.");
					$return_val = false;
				}
			}
		}

		return $return_val;
	}

	public function checkKerberosPrincipalUserExists() {
		_log_check_error("Not implemented");
		return false;
	}

	public function checkAutomountHomeDirectoryUserExists() {
		_log_check_error("Not implemented");
		return false;
	}

}
