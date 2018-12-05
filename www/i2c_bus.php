<?php
	// class to deal with i2c communications on the raspberry pi
	//
	//	- this class makes use of i2c-tools ( see readme for installation instructions )
	//		- http://www.acmesystems.it/i2c
	//
	class i2c_bus
	{
		
		private $block = 1; 	// the i2c block ( 0 on first gen rpi's, 1 on subsequnet rpi's )
		private $i2c_address;	// the i2c bus address of the unit being communicated with ( set when instantiated )
		
		function __construct(
			$i2c_address // the i2c address of the device we're communicating with
		) {
			$this->i2c_address = $i2c_address;
			
			///$this->i2c = fopen( '/dev/i2c-' . $this->block, "w+b" );
			
			/*	// read
				$address = ($address | 0x01) << 8 & $registry;
				$i2c = fopen("/dev/i2c-2", "w+b");
				fseek($i2c, $address)
				$rtn = fread($i2c, $length)
				fclose($i2c);
			
				// write to chip
				$address = ($address & 0xFE) << 8 & $registry;
				$i2c = fopen("/dev/i2c-2", "w+b");
				fseek($i2c, $address)
				fwrite($i2c, $data, $length)
				fclose($i2c);
			*/
			
		}
		
		function __destruct() {
			//pclose( $this->i2c );
		}
	// --- READERS --------------------------------------------------------------------
	
		
		public function read_register(
			$register	// register location ( eg. 0x29 )
		) {
                    //echo ('i2cget -y ' . $this->block . ' ' . $this->i2c_address . ' ' . $register)."<BR>";
                    //echo (shell_exec( 'i2cget -y ' . $this->block . ' ' . $this->i2c_address . ' ' . $register ) );
                    return trim( shell_exec( 'i2cget -y ' . $this->block . ' ' . $this->i2c_address . ' ' . $register ) );
		}
				
		public function read_two_bytes(
                        $msb_register // most significant byte register location
                ) {
                    // read first byte
                    $msb = intval($this->read_register($msb_register),16);
                    // ask Arduino for send the second byte
			echo "lecture du premier octet : ".$msb."<BR>";
                    $this->write_register("",255);
                    sleep(1);
                    $lsb = intval($this->read_register($msb_register),16);
			echo "lecture du deuxieme octet : ".$lsb."<BR>";
                    $val = ($msb +255)+$lsb ;
			echo "première valeur transformée : ".$val."<BR>";
                    return $val;
                    
                }

                public function read_signed_short(
			$msb_register	// most significant byte register location
		) {
			echo "valeur de l'adresse du premier registre : ".$msb_register."<BR>";
                        $msb = intval( $this->read_register( $msb_register ), 16 );
			echo "lecture du premier octer à l'adresse ci-dessus : ".$msb."<BR>";
                        
                        $lsb = intval( $this->read_register( $msb_register + 1 ), 16 );
			echo "lecture du deuxieme octer à l'adresse +1 : ".$lsb."<BR>";
                        $val = ( $msb << 8 ) + $lsb;
			echo "première valeur transformée : ".$val."<BR>";
                        $array = unpack( 's', pack( 'v', $val ) );
			echo "deuxième transformation ??? : ".$array[1]."<BR>";
                        $decimal_value = $array[1];
			return $decimal_value;
		}
		
		public function read_unsigned_short(
			$msb_register	// most significant byte register location
		) {
			$msb = intval( $this->read_register( $msb_register ), 16 );
			$lsb = intval( $this->read_register( $msb_register + 1 ), 16 );
			$val = ( $msb << 8 ) + $lsb;
			$array = unpack( 'S', pack( 'v', $val ) );
			$decimal_value = $array[1];
			return $decimal_value;
		}
		
		public function read_unsigned_long(
			$msb_register	// most significant byte register location
		) {
			$msb = intval( $this->read_register( $msb_register ), 16 );
			$lsb = intval( $this->read_register( $msb_register + 1 ), 16 );
			$xlsb = intval( $this->read_register( $msb_register + 2 ), 16 );
			$val = ( $msb << 16 ) + ( $lsb << 8 ) + $xlsb;
			$array = unpack( 'l', pack( 'V', $val ) );
			$decimal_value = $array[1];
			return $decimal_value;
		}
		
		
	// --- WRITERS --------------------------------------------------------------------
	
		
		public function write_register(
			$register,	// register address ( eg. 0x29 )
			$value		// value to set at register address ( must be a decimal value, eg. 10001001 should be passed as 
		) {
			//echo ( 'i2cset -y ' . $this->block . ' ' . $this->i2c_address . ' ' . $register . ' ' . $value );
                        shell_exec( 'i2cset -y ' . $this->block . ' ' . $this->i2c_address . ' ' . $register . ' ' . $value );
		}
		
	}
	
?>