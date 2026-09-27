<?

class encryption{
  public $password;

  private static $_INSTANCE;

  /**
   * Class constructor.
   */
  public function __construct(){
    
  }

  public function __destruct(){
    
  }

  public static function get_instance() {
    if(self::$_INSTANCE == null) {
      $_INSTANCE = new encryption();
    }
    return $_INSTANCE;
  }

  # const KEY_PATH = "/var/local/hotline/hotlineInfo.txt";


  public function getPw($keypath = "/var/local/pa/paInfo.txt")
  {
	/*
	$fp = fopen($keypath,"r"); 
    while( !feof($fp) )
        $doc_data = fgets($fp); 
    fclose($fp); 
    return $doc_data;
	*/
	return "paecncrtpy2020hanbom!pwddecrypt!";
  }

  public function encrypt_oneway($plainText){
    $this->getPw();
    $hashed = base64_encode(hash('sha256', $plainText, true));
    echo $hashed;
  }

  public function encrypt($plainText){
    $password = $this->getPw();


	$password = substr(hash('sha256', $password, true), 0, 32);
    // Initial Vector(IV)는 128 bit(16 byte)입니다.
    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

    // 암호화
    $encrypted = base64_encode(openssl_encrypt($plainText, 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $iv));
    return $encrypted;

  }

  public function decrypt($encrypted){
    $password = $this->getPw();
    $password = substr(hash('sha256', $password, true), 0, 32);
    // Initial Vector(IV)는 128 bit(16 byte)입니다.
    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

    // 복호화
    $decrypted = openssl_decrypt(base64_decode($encrypted), 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $iv);
    return $decrypted;
  }

  public function encryptPw($plainText,$password){
    if(empty($password)) {
      $password = $this->getPw();
    }
   
    $password = substr(hash('sha256', $password, true), 0, 32);
    // Initial Vector(IV)는 128 bit(16 byte)입니다.
    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

    // 암호화
    $encrypted = base64_encode(openssl_encrypt($plainText, 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $iv));
    return $encrypted;
  }

  public function decryptPw($encrypted,$password){
    if(empty($password)) {
      $password = $this->getPw();
    }
   
    $password = substr(hash('sha256', $password, true), 0, 32);
    // Initial Vector(IV)는 128 bit(16 byte)입니다.
    $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

    // 복호화
    $decrypted = openssl_decrypt(base64_decode($encrypted), 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $iv);
    return $decrypted;
  }

    function randomString($length)  
    {  
        $characters  = "0123456789";  
        $characters .= "abcdefghijklmnopqrstuvwxyz";  
        $characters .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";  
        //$characters .= "_";  
        
        $string_generated = "";  
        
        $nmr_loops = $length;  
        while ($nmr_loops--)  
        {  
            $string_generated .= $characters[mt_rand(0, strlen($characters) - 1)];  
        }  
        return $string_generated;  
    }
}

?>
