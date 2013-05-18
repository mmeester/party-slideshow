<?php
/**
 * Detailed description will follow later on.
 *
 * @author     Maurits Meester <mmeester [at] e-mmer [dot] nl>
 */
 
class User
{
	public $userData;
	private $userSettings;
	private $hasUserSettings = false;

	public function __construct($userData)
	{
		$this->userData = $userData;
	}
	
	public static function get_by_email( $email )
	{
		global $sql;
		
		$cursor = $sql->prepareQuery("
				SELECT *
				FROM users
				WHERE email = ':email'
				LIMIT 1
			");
		$sql->bindInput($cursor, 'email', $email);
		
		$usersFound = $sql->executeQueryRowCount($cursor);
		
		$userData = $sql->executeQueryArray($cursor);
		$sql->freeCursor($cursor);
		
		if ($usersFound === 0)
			return false;
			
		return $userData[0];
	}
	
	public static function get_by_id( $id, $anonymous=0 )
	{
		global $sql;
		
		$cursor = $sql->prepareQuery("
				SELECT *
				FROM users
				WHERE id = ':id' AND anonymous = ':anonymous'
				LIMIT 1
			");
		$sql->bindInput($cursor, 'id', $id);
		$sql->bindInput($cursor, 'anonymous', $anonymous);
		
		$usersFound = $sql->executeQueryRowCount($cursor);
		
		$userData = $sql->executeQueryArray($cursor);
		$sql->freeCursor($cursor);
		
		if ($usersFound === 0)
			return false;
			
		return $userData[0];
	}
	
	public static function get_all( $anonymous=0 )
	{
		global $sql;
		
		$cursor = $sql->prepareQuery("
				SELECT *
				FROM users
				WHERE anonymous = ':anonymous'
			");
		$sql->bindInput($cursor, 'anonymous', $anonymous);
		
		$usersFound = $sql->executeQueryRowCount($cursor);
		
		$userData = $sql->executeQueryArray($cursor);
		$sql->freeCursor($cursor);
		
		if ($usersFound === 0)
			return false;
			
		return $userData;
	}
	
	public static function get_by_fb_id( $fb_id )
	{
		global $sql;
		
		$cursor = $sql->prepareQuery("
				SELECT *
				FROM users
				WHERE fb_id = ':fb_id'
				LIMIT 1
			");
		$sql->bindInput($cursor, 'fb_id', $fb_id);
		
		$usersFound = $sql->executeQueryRowCount($cursor);
		
		$userData = $sql->executeQueryArray($cursor);
		$sql->freeCursor($cursor);
		
		if ($usersFound === 0)
			return false;
			
		return $userData[0];
	}
	
	public static function add( $first_name, $email, $name, $phone="", $last_name="", $middle_name="", $gender="", $street="", $streetnumber="", $streetnumber_suffix="", $zipcode="", $city="", $optin="", $company="", $rest_data="", $fb_id="", $birthday="" )
	{
		global $sql;
		
			$cursor = $sql->prepareQuery("
				INSERT INTO users (fb_id, name, first_name, middle_name, last_name, email, gender, street, streetnumber, streetnumber_suffix, zipcode, city, optin, birthday, phone, rest_data,  ip, created_time, updated_time, company )
                VALUES (':fb_id', ':name', ':first_name', ':middle_name', ':last_name', ':email', ':gender', ':street', ':streetnumber', ':streetnumber_suffix', ':zipcode', ':city', ':optin', ':birthday', ':phone', ':rest_data', ':ip', NOW(), NOW(), ':company')
            ");
      $sql->bindInput($cursor, 'fb_id' , $fb_id);
      $sql->bindInput($cursor, 'name' , $name);
      $sql->bindInput($cursor, 'first_name' , $first_name);
      $sql->bindInput($cursor, 'middle_name' , $middle_name);
      $sql->bindInput($cursor, 'last_name' , $last_name);
      $sql->bindInput($cursor, 'email' , $email);
      $sql->bindInput($cursor, 'gender' , $gender);
      $sql->bindInput($cursor, 'street' , $street);
      $sql->bindInput($cursor, 'streetnumber' , $streetnumber);
      $sql->bindInput($cursor, 'streetnumber_suffix' , $streetnumber_suffix);
      $sql->bindInput($cursor, 'zipcode' , $zipcode);
      $sql->bindInput($cursor, 'optin', $optin);
      $sql->bindInput($cursor, 'city' , $city);
      $sql->bindInput($cursor, 'birthday' , $birthday);
      $sql->bindInput($cursor, 'phone' , $phone);
      $sql->bindInput($cursor, 'rest_data' , $rest_data);
      $sql->bindInput($cursor, 'ip' , getRealIpAddr());
      $sql->bindInput($cursor, 'company' , $company);
      
      
      $user_id = intval($sql->executeQueryInsertId($cursor));
			$sql->freeCursor($cursor);
           
			return $user_id;
	}
	
	public static function update( $user_id, $first_name, $email, $name, $phone="", $last_name="", $middle_name="", $gender="", $street="", $streetnumber="", $streetnumber_suffix="", $zipcode="", $city="", $optin="", $company="", $rest_data="", $fb_id="", $birthday="" )
	{
		global $sql;
		
			$cursor = $sql->prepareQuery("
				UPDATE users SET
							fb_id = ':fb_id',
							name = ':name',
							first_name = ':first_name',
							middle_name = ':middle_name',
							last_name = ':last_name',
							email = ':email',
							gender = ':gender',
							street = ':street',
							streetnumber = ':streetnumber', 
							streetnumber_suffix = ':streetnumber_suffix',
							zipcode = ':zipcode',
							optin = ':optin',
							city = ':city',
							birthday = ':birthday',
							rest_data =  ':rest_data',
							ip = ':ip',
							updated_time = NOW(),
							company = ':company'
         WHERE id = ':user_id'
            ");
      $sql->bindInput($cursor, 'fb_id' , $fb_id);
      $sql->bindInput($cursor, 'name' , $name);
      $sql->bindInput($cursor, 'first_name' , $first_name);
      $sql->bindInput($cursor, 'middle_name' , $middle_name);
      $sql->bindInput($cursor, 'last_name' , $last_name);
      $sql->bindInput($cursor, 'email' , $email);
      $sql->bindInput($cursor, 'gender' , $gender);
      $sql->bindInput($cursor, 'street' , $street);
      $sql->bindInput($cursor, 'streetnumber' , $streetnumber);
      $sql->bindInput($cursor, 'streetnumber_suffix' , $streetnumber_suffix);
      $sql->bindInput($cursor, 'zipcode' , $zipcode);
      $sql->bindInput($cursor, 'optin', $optin);
      $sql->bindInput($cursor, 'city' , $city);
      $sql->bindInput($cursor, 'birthday' , $birthday);
      $sql->bindInput($cursor, 'rest_data' , $rest_data);
      $sql->bindInput($cursor, 'user_id' , $user_id);
      $sql->bindInput($cursor, 'ip' , getRealIpAddr());
      $sql->bindInput($cursor, 'company' , $company);
      
      $update = $sql->executeQuery($cursor);
			$sql->freeCursor($cursor);
			
			if($phone!==''){
				$cursor = $sql->prepareQuery("
				UPDATE users SET
							phone = ':phone'
         WHERE id = ':user_id'
            ");
				$sql->bindInput($cursor, 'phone' , $phone);
				$sql->bindInput($cursor, 'user_id' , $user_id);
				
				$update = $sql->executeQuery($cursor);
				$sql->freeCursor($cursor);
			}			
						
			if(!$update)
			{
				return true;
			}
			else
			{
				return false;
			}
	}
	
}