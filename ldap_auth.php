<?php
  // These are inputs
  $username = 'USERNAME';
  $password = 'PASSWORD';

  $rdn = 'uid='.$username.',ou=People,o=Org';
  $link = ldap_connect('ldap://ldap.domain/');
  ldap_set_option($link, LDAP_OPT_PROTOCOL_VERSION, 3);
  if ($link == FALSE) { die('connect failed'); }
  if (ldap_bind($link, $rdn, $password) == FALSE)
  { echo 'authentication failed'."\n"; }
  else
  {
   echo 'authentication succeeded'."\n";
   // Do what you want here

   // This is an example that retrieves the name of the user
   $res = ldap_read($link, $rdn, '(objectClass=*)', array('cn'));
   $entry = ldap_get_entries($link, $res);
   echo 'name: '.$entry[0]['cn'][0]."\n";
  }
  ldap_close($link);
?>
