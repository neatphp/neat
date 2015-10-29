<?php
namespace Neat\Util;

/**
 * Security utility
 */
class Security
{
    /** @var bool|false */
    private $mcrpytEnabled = false;

    /** @var string */
	private $mode = MCRYPT_MODE_CBC;

    /** @var string */
    private $algorithm = MCRYPT_3DES;

    /**
     * Constructor.
     *
     * @param bool|true $mcrpytEnabled
     * @param string    $mode          MCRYPT mode
     * @param string    $algorithm     MCRYPT algorithm
     */
    public function __construct($mcrpytEnabled = true, $mode = MCRYPT_MODE_CBC, $algorithm = MCRYPT_3DES)
    {
        $this->mcrpytEnabled = $mcrpytEnabled;
        $this->mode = $mode;
        $this->algorithm = $algorithm;
    }

	/**
	 * Encrypts a string.
	 *
	 * @param string $string
	 * @param string $key
	 *
	 * @return string
	 */
	public function encrypt($string, $key)
	{
		if($this->mcrpytEnabled) {
			return $this->mcryptEncrypt($string, $key);
		} else {
			return $this->xorEncrypt($string, $key);
		}
	}


	/**
	 * Decrypts a string.
	 *
	 * @param string $string
	 * @param string $key
	 *
	 * @return string
	 */
	public function decrypt($string, $key)
	{
		if($this->mcrpytEnabled) {
			return $this->mcryptDecrypt($string, $key);
		} else {
			return $this->xorDecrypt($string, $key);
		}
	}

	/**
	 * Encrypts a string using MCRYPT.
	 *
	 * @param string $string
	 * @param string $key
	 *
	 * @return string
	 */
	private function mcryptEncrypt($string, $key)
	{
		$module = mcrypt_module_open($this->algorithm, '', $this->mode, '');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($module), MCRYPT_RAND);
		$hash = substr(sha1($key), 0, mcrypt_enc_get_key_size($module));
		mcrypt_generic_init($module, $hash, $iv);
		$enc = $iv . mcrypt_generic($module, $string);
		mcrypt_generic_deinit($module);
		mcrypt_module_close($module);

		$enc = $this->addNoise($enc, $key);
		$enc = base64_encode($enc);

		return $enc;
	}

	/**
	 * Decrypts a string using MCRYPT.
	 *
	 * @param string $str
	 * @param string $key
	 *
	 * @return string
	 */
	private function mcryptDecrypt($str, $key)
	{
		$str = base64_decode($str);
		$str = $this->removeNoise($str, $key);

		$module = mcrypt_module_open($this->algorithm, '', $this->mode, '');
		$size = mcrypt_enc_get_iv_size($module);
		$iv = substr($str, 0, $size);
		$str = substr($str, $size);
		$hash = substr(sha1($key), 0, mcrypt_enc_get_key_size($module));
		mcrypt_generic_init($module, $hash, $iv);
		$dec = mdecrypt_generic($module, $str);
		$dec = rtrim($dec,"\0");
		mcrypt_generic_deinit($module);
		mcrypt_module_close($module);

		return $dec;
	}

	/**
	 * Encrypts a string using XOR.
	 *
	 * @param string $string
	 * @param string $key
	 *
	 * @return string
	 */
	private function xorEncrypt($string, $key)
	{
		$chars = str_split($string);
		$hash = sha1($key);

		$rand = '';
		for ($i = 0; $i < 40; $i++) $rand .= mt_rand(0, mt_getrandmax());
		$rand = sha1($rand);

		$tmp = array();
		foreach ($chars as $index => $char) {
			$randChar = substr($rand, $index % 40, 1);
			$tmp[] = $randChar;
			$tmp[] = $randChar ^ $char;
		}

		$enc = '';
		foreach ($tmp as $index => $char) {
			$enc .= $char ^ substr($hash, $index % 40, 1);
		}

		$enc = $this->addNoise($enc, $key);
		$enc = base64_encode($enc);

		return $enc;
	}

	/**
	 * Decrypts a string using XOR.
	 *
	 * @param string $string
	 * @param string $key
	 *
	 * @return string
	 */
	private function xorDecrypt($string, $key)
	{
        $string = base64_decode($string);
        $string = $this->removeNoise($string, $key);

		$chars = str_split($string);
		$hash = sha1($key);

		foreach ($chars as $index => & $char) {
			$char = $char ^ substr($hash, $index % 40, 1);
		}

		$dec = '';
		while($chars) {
			$dec .= array_shift($chars) ^ array_shift($chars);
		}

		return $dec;
	}

	/**
	 * Adds permuted noise to the encrypted string to protect
	 * against Man-in-the-middle attacks on CBC mode ciphers.
	 *
	 * @param string $string
	 * @param string $key
	 *
	 * @return string
	 */
	private function addNoise($string, $key)
	{
		$len = strlen($string);
		$key = sha1($key);

		$return = '';
		for ($i = 0, $j = 0; $i < $len; $i++, $j++) {
			if ($j == 40) $j = 0;
			$return .= chr((ord($string[$i]) + ord($key[$j])) % 256);
		}

		return $return;
	}

	/**
	 * Removes permuted noise from the encrypted string.
	 *
	 * @param string $string
	 * @param string $key
	 *
	 * @return string
	 */
	private function removeNoise($string, $key)
	{
		$len = strlen($string);
		$key = sha1($key);

		$return = '';
		for ($i = 0, $j = 0; $i < $len; $i++, $j++) {
			if ($j == 40) $j = 0;
			$tmp = ord($string[$i]) - ord($key[$j]);
			if ($tmp < 0) $tmp = $tmp + 256;
			$return .= chr($tmp);
		}

		return $return;
	}
}