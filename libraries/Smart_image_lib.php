<?php
/**
 * Image resizing and watermarking class
 * @auther Mehdiyev Rauf aka mra214 (mra214@rambler.ru) 24.09.2009
 *
 * setImageFile()      - Sets source image file (full path).
 * setCacheDir()       - Sets cache directory. Must be writable, it won't warn or generate error about.
 * setCacheTime()      - Sets caching time for images in cache directory in seconds. Default is 0 (infinitely).
 * setWidth()          - Sets width for resizing.
 * setHeight()         - Sets height for resizing.
 * setDestIType()      - Sets return type of the altered image.
 * setCropV()          - Sets vertical cropping of the image if it doesn't wholly suite image dimensions. Can be t, m, b (top, middle, bottom). Default is t.
 * setCropH()          - Sets horizontally cropping of the image if it doesn't wholly suite image dimensions. Can be l, c, r (left, center, right). Default is c.
 * resize()            - Resizes image. If proportionally resized image doesn't wholly suite to the provided dimensions, it will be automatically cropped.
 * setWMOpacity()      - Sets opacity level of the watermark image. Can be 0-100. Default level is 100 (not transparent)
 * setWMVAlingment()   - Sets Vertical alignment of the watermark. Can be t, m, b
 * setWMHAlingment()   - Sets Vertical alignment of the watermark. Can be t, m, b
 * setWMMarginTop()    - Sets top margin of the watermark image. It will affect only if watermark vertical alignment set to t (top)
 * setWMMarginRight()  - Sets right margin of the watermark image. It will affect only if watermark horizotal alignment set to r (right)
 * setWMMarginBottom() - Sets bottom margin of the watermark image. It will affect only if watermark vertical alignment set to b (top)
 * setWMMarginLeft()   - Sets left margin of the watermark image. It will affect only if watermark horizotal alignment set to l (left)
 * addWM()             - Adds watermark to the image. You can add multiple watermarks at the same time.
 * getImage()          - Returns resulting image to the browser. It automatically cals saveImage functions if cache directory is defined.
 * saveImage()         - Saves image to the cache directory if it is defined.
 */
class Smart_image_lib{

	public function setImageFile($src){
		$src = str_replace(array('`','\\'), array('','/'), $src);
		try{
			list($this->sourceW, $this->sourceH) = $this->getImageDimensions($src);
			$this->sourceIExt = $this->getImageExt($src);
			if(!$this->destIType);
				$this->setDestIType($this->sourceIExt);
			$this->sourceI = $this->createImageFromFile($src);
		}catch(Exception $e){
			$this->errorHandler($e);
		}
		$this->sourceImageFile = $src;
	}
	
	public function setWidth($width){
		try{
			$width = (int)str_replace('-','',$width);
			if($width)
				$this->destW = $width;
			else
				throw new Exception('Width must be type of integer');
		}catch(Exception $e){
			$this->errorHandler($e);
		}
	}
	
	public function setHeight($height){
		try{
			$height = (int)str_replace('-','',$height);
			if($height)
				$this->destH = $height;
			else
				throw new Exception('Height must be type of integer');
		}catch(Exception $e){
			$this->errorHandler($e);
		}
	}

	public function resize(){
		if(file_exists($this->cacheDir.md5($this->sourceImageFile).'_'.$this->destW.'x'.$this->destH.'.'.$this->destIType)) return;
		
		$cropX = 0;
		$cropY = 0;
		
		if(empty($this->destW) && empty($this->destH)){
			exit;
		}
		elseif($this->destW == $this->destH){
			$ratio = $this->destW/min($this->sourceW, $this->sourceH);
			list($cropX, $cropY) = $this->getCropAreas();
			$newW = ceil($this->sourceW*$ratio);
			$newH = ceil($this->sourceH*$ratio);
		}
		elseif($this->destW < $this->destH && $this->sourceW > $this->sourceH){
			$ratio = $this->sourceH/$this->destH;
			$newW = ceil($this->sourceW/$ratio);
			$newH = ceil($this->sourceH/$ratio);
				if(empty($this->destW))
					$this->destW = $newW;
			if(!is_numeric($this->destW)) $this->destW = $newH;
			list($cropX, $cropY) = $this->getCropAreas($newW,$newH,$ratio);
		}
		elseif($this->destW < $this->destH && $this->sourceW < $this->sourceH){
			if(empty($this->destW))
				$ratio = $this->sourceH/$this->destH;
			else
				$ratio = $this->sourceW/$this->destW;
			$newW = ceil($this->sourceW/$ratio);
			$newH = ceil($this->sourceH/$ratio);
			if(empty($this->destW))
				$this->destW = $newW;
			if($this->destH > $newH){
				$ratio = $this->sourceH/$this->destH;
				$newW = ceil($this->sourceW/$ratio);
				$newH = ceil($this->sourceH/$ratio);
			}
			if(!is_numeric($this->destW)) $this->destW = $newH;
			list($cropX, $cropY) = $this->getCropAreas($newW,$newH,$ratio);
		}
		elseif($this->destW > $this->destH && $this->sourceW > $this->sourceH){
			$ratio = $this->sourceW/$this->destW;
			$newW = ceil($this->sourceW/$ratio);
			$newH = ceil($this->sourceH/$ratio);
			if($this->destH > $newH){
				$ratio = $this->sourceH/$this->destH;
				$newW = ceil($this->sourceW/$ratio);
				$newH = ceil($this->sourceH/$ratio);
			}
			if(!is_numeric($this->destH)) $this->destH = $newH;
			list($cropX, $cropY) = $this->getCropAreas($newW,$newH,$ratio);
		}
		else{
			$ratio = $this->sourceW/$this->destW;
			$newW = ceil($this->sourceW/$ratio);
			$newH = ceil($this->sourceH/$ratio);
			if(!is_numeric($this->destH)) $this->destH = $newH;
			list($cropX, $cropY) = $this->getCropAreas($newW,$newH,$ratio);
		}
		
		//echo md5($this->sourceImageFile).' R: '.$ratio.' | W: '.$newW.' | H: '.$newH.' C: '.$cropX.' x '.$cropY.'<br>';
		$this->destI = imagecreatetruecolor($this->destW, $this->destH);
		imagecopyresampled($this->destI, $this->sourceI, 0, 0, $cropX, $cropY, $newW, $newH, $this->sourceW, $this->sourceH);
	}
	
	public function getImage(){
		$imageInCache = $this->isImageInCache();
		//if(!$imageInCache)$this->saveImage();
		switch($this->destIType){
			case 'jpg':
			case 'jpeg':
				header('Content-type: image/jpeg');
				break;
			case 'png':
				header('Content-type: image/x-png');
				break;
			case 'gif':
				header('Content-type: image/gif');
				break;
			default:
				header('Content-type: image/jpeg');
		}
		
			header('X-Powered-By:');
			header('Set-Cookie:');
		
		if($f = $imageInCache){
//			$mtime = filemtime($f);
//			$expires = $mtime + $this->cacheTime;
//			header('Pragma: public');
//			header('Cache-Control: maxage='.$expires);
//			header('Expires: ' . gmdate('D, d M Y H:i:s', $expires) . ' GMT');
//			header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', $mtime));
//			header('Content-Length: '.filesize($f));
//			
//			
//				$CI =& get_instance();
//				$CI->load->database();
//				$CI->db->set('request_method', $_SERVER['REQUEST_METHOD'])
//						->set('time', date('Y-m-d H:i:s'))
//						->insert('test');
//			
//			if($_SERVER['REQUEST_METHOD'] == 'HEAD'){
//				exit();
//				$CI =& get_instance();
//				$CI->load->database();
//				$CI->db->set('request_method', $_SERVER['REQUEST_METHOD'])
//						->set('time', date('Y-m-d H:i:s'))
//						->insert('test');
//			}
			
			$fd = fopen($f,'rb');
			$sz = filesize($f);
			echo fread($fd,$sz);
			fclose($fd);
		}else{
			$i = $this->getImageObject();
			switch($this->destIType){
				case 'jpg':
				case 'jpeg':
					imagejpeg($i);
					break;
				case 'png';
					imagepng($i);
					break;
				case 'gif':
					imagegif($i);
					break;
				default:
					imagejpeg($i);
			}
		}
	}
	
//	public function saveImage(){
//		if($this->cacheDir && !$this->isImageInCache()){
//			$f = $this->genFileNameForCaching();
//			switch($this->destIType){
//				case 'jpg':
//				case 'jpeg':
//					$r = imagejpeg($this->getImageObject(), $f, $this->quality);
//					break;
//				case 'png';
//					$r = imagepng($this->getImageObject(), $f, 9);
//					break;
//				case 'gif':
//					$r = imagegif($this->getImageObject(), $f);
//					break;
//			}
//			if($r)
//				return $f;
//			return false;
//		}
//		return true;
//	}


//	public function saveImage(){
//		$f = $this->isImageInCache();
//		if($this->cacheDir && !$f){
//			$f = $this->genFileNameForCaching();
//			switch($this->destIType){
//				case 'jpg':
//				case 'jpeg':
//					$r = imagejpeg($this->getImageObject(), $f, $this->quality);
//					break;
//				case 'png';
//					$r = imagepng($this->getImageObject(), $f, 9);
//					break;
//				case 'gif':
//					$r = imagegif($this->getImageObject(), $f);
//					break;
//			}
//			if($r)
//				return $f;
//			return false;
//		}
//		return $f;
//	}


	public function saveImage($fileName=false){
		if($fileName){
			$f = $fileName;
		}else{
			$f = $this->genFileNameForCaching();
		}
		if(($this->cacheDir && !$f) || $f){
		if(!$f){
			$f = $this->genFileNameForCaching();
		}
			switch($this->destIType){
				case 'jpg':
				case 'jpeg':
					$r = imagejpeg($this->getImageObject(), $f, $this->quality);
					break;
				case 'png';
					$r = imagepng($this->getImageObject(), $f, 9);
					break;
				case 'gif':
					$r = imagegif($this->getImageObject(), $f);
					break;
			}
			if($r)
				return $f;
			return false;
		}
		return $f;
	}
	
	public function setAllowedImageTypes($types){
		if($types){
			if(gettype($types)=='array')
				$this->allowedImageTypes = $types;
			else
				$this->allowedImageTypes = (array)$types;
		}
	}
	
	public function setDestIType($type){
		if(in_array(strtolower($type), $this->allowedImageTypes))
			$this->destIType = $type;
		else
			throw new Exception('Not allowed image type: '.htmlspecialchars($type));
	}
	
	public function setCacheDir($dir){
		if(file_exists($dir) && is_writable($dir))
			$this->cacheDir = $dir;
	}
	
	public function setCacheTime($sec){
		if(is_int($sec))
			$this->cacheTime = $sec;
	}
	
	public function setCropV($cropFrom){
		$cropFrom = strtolower($cropFrom);
		if(in_array($cropFrom, array('t','m','b')))
			$this->cropV = $cropFrom;
		else
			$this->cropV = 't';
	}
	
	public function setCropH($cropFrom){
		$cropFrom = strtolower($cropFrom);
		if(in_array($cropFrom, array('l','c','r')))
			$this->cropH = $cropFrom;
		else
			$this->cropH = 'c';
	}
	
	public function setQuality($quality){
		if(is_int($quality) && $quality > 0 && $quality <= 100)
			$this->quality = $quality;
		else
			$this->quality = 90;
	}
	
	public function addWM($src){
		$src = str_replace(array('`','\\'), array('','/'), $src);
		$this->wmImageFile = $src;
		if(!$this->isImageInCache()){
			try{
				list($this->wmWidth, $this->wmHeight) = $this->getImageDimensions($src);
				if(gettype($this->destI)=='resource'){
					$sourceImage =& $this->destI;
					$sourceWidth = $this->destW;
					$sourceHeight = $this->destH;
				}else{
					$sourceImage =& $this->sourceI;
					$sourceWidth = $this->sourceW;
					$sourceHeight = $this->sourceH;
				}
				list($minX, $maxX, $minY, $maxY) = $this->getWMBoundaries($sourceWidth, $sourceHeight);
				$this->wmI = $this->createImageFromFile($src);

				$this->waterMarkedImage = imagecreatetruecolor($sourceWidth, $sourceHeight);		

				for( $y = 0; $y < $sourceHeight; $y++ ) {
					  for( $x = 0; $x < $sourceWidth; $x++ ) {
							$returnColor = NULL;
							$wmX = $x - $minX;
							$wmY = $y - $minY;
							$mainRGB = imagecolorsforindex( $sourceImage, imagecolorat( $sourceImage, $x, $y ) );

							if (  $wmX >= 0 && $wmX < $this->wmWidth && $wmY >= 0 && $wmY < $this->wmHeight ) {
								  $wmRGB = imagecolorsforindex( $this->wmI, imagecolorat( $this->wmI, $wmX, $wmY ) );

								  $wmAlpha  = round( ( ( 127 - $wmRGB['alpha'] ) / 127 ), 2 );
								  $wmAlpha  = $wmAlpha * $this->wmAlphaLevel;

								  $avgRed  = $this->getAvgColor($mainRGB['red'], $wmRGB['red'], $wmAlpha);
								  $avgGreen = $this->getAvgColor($mainRGB['green'], $wmRGB['green'], $wmAlpha);
								  $avgBlue = $this->getAvgColor($mainRGB['blue'], $wmRGB['blue'], $wmAlpha);

								  $returnColor = $this->getImageColor($this->waterMarkedImage, $avgRed, $avgGreen, $avgBlue);
							} else {
								  $returnColor = imagecolorat($sourceImage, $x, $y);
							}
							imagesetpixel($this->waterMarkedImage, $x, $y, $returnColor);
					  }
				}
				
				imagedestroy($sourceImage);
				$sourceImage = $this->waterMarkedImage;
				
			}catch(Exception $e){
				$this->errorHandler($e);
			}
		}
	}
	
	public function setWMHAlingment($a){
		if(in_array($a, array('l','c','r')))
			$this->wmH = $a;
	}
	
	public function setWMVAlingment($a){
		if(in_array($a, array('t','m','b')))
			$this->wmV = $a;
	}
	
	public function setWMMarginTop($marg){
		$this->wmMarginTop = (int)$marg;
	}
	
	public function setWMMarginRight($marg){
		$this->wmMarginRight = (int)$marg;
	}
	
	public function setWMMarginBottom($marg){
		$this->wmMarginBottom = (int)$marg;
	}
	
	public function setWMMarginLeft($marg){
		$this->wmMarginLeft = (int)$marg;
	}
	
	public function setWMOpacity($opacity){
		if(is_numeric($opacity) && $opacity >= 0 && $opacity <= 100)
			$this->wmAlphaLevel = $opacity / 100;
	}
	
	public function __construct(){
		if(!extension_loaded('GD') || !function_exists('gd_info'))
			die('GD extension is not loaded!');
	}
	
	public function __destruct(){
		@imagedestroy($this->sourceI);
		@imagedestroy($this->destI);
	}
	
//-------------------------------------------------------------------------------------------------------------//
	private function genFileNameForCaching(){
		return $this->cacheDir.md5($this->sourceImageFile.(string)$this->wmImageFile).'_'.$this->destW.'x'.$this->destH.'.'.$this->destIType;
	}
	private function isImageInCache(){
		if($this->cacheDir){
			$f = $this->genFileNameForCaching();
			if( file_exists($f) && filemtime($f)>filemtime($this->sourceImageFile) && ( $this->cacheTime == 0 || ($this->cacheTime > (time()-filemtime($f))) ) )
				return $f;
		}
		return false;
	}
	private function getImageObject(){
		if(gettype($this->destI)=='resource')
			return $this->destI;
		return $this->sourceI;
	}
	private function getWMBoundaries($sourceWidth, $sourceHeight){
		switch($this->wmH){
			case 'l':
				$minX = $this->wmMarginLeft;
				$maxX = $this->wmMarginLeft + $this->wmWidth;
				break;
			case 'c':
				$minX = floor($sourceWidth/2-$this->wmWidth/2);
				$maxX = ceil($sourceWidth/2+$this->wmWidth/2);
				break;
			case 'r':
				$minX = $sourceWidth - $this->wmWidth - $this->wmMarginRight;
				$maxX = $sourceWidth - $this->wmMarginRight;
				break;
			default:
				$minX = $sourceWidth - $this->wmWidth - $this->wmMarginRight;
				$maxX = $sourceWidth - $this->wmMarginRight;
		}
		switch($this->wmV){
			case 't':
				$minY = $this->wmMarginTop;
				$maxY = $this->wmMarginTop + $this->wmHeight;
				break;
			case 'm':
				$minY = floor($sourceHeight/2-$this->wmHeight/2);
				$maxY = ceil($sourceHeight/2+$this->wmHeight/2);
				break;
			case 'b':
				$minY = $sourceHeight - $this->wmHeight - $this->wmMarginBottom;
				$maxY = $sourceHeight - $this->wmMarginBottom;
				break;
			default:
				$minY = $sourceHeight - $this->wmHeight - $this->wmMarginBottom;
				$maxY = $sourceHeight - $this->wmMarginBottom;
		}
		return array($minX, $maxX, $minY, $maxY);
	}
	
	private function getAvgColor($colorA, $colorB, $alphaLevel){
		return round($colorA*(1-$alphaLevel)+$colorB*$alphaLevel);
	}
	private function getImageColor($im, $r, $g, $b){
		$c = imagecolorexact($im, $r, $g, $b);
		if ($c!=-1)
			return $c;
		$c=imagecolorallocate($im, $r, $g, $b);
		if ($c!=-1)
			return $c;
		return imagecolorclosest($im, $r, $g, $b);
	}
	private function getCropAreas($newW=false, $newH=false, $ratio=false){

		if(!is_int($this->cropV)){
			switch($this->cropV){
				case 't':
					$cropY = 0;
					break;
				case 'm':
					if(!$newH){
						if($this->sourceW < $this->sourceH){
							$cropY = floor(($this->sourceH-$this->sourceW)/2);
						}else{
							$cropY = 0;
						}
					}else{
						$cropY = floor(($this->sourceH-$this->destH*$ratio)/2);
					}
					break;
				case 'b':
					if(!$newH){
						if($this->sourceW < $this->sourceH){
							$cropY = floor(($this->sourceH-$this->sourceW));
						}else{
							$cropY = 0;
						}
					}else{
						$cropY = floor($this->sourceH-$this->destH*$ratio);
					}
					break;
				default:
					$cropY = 0;
			}
		}else{
			$cropY = $this->cropV;
		}
		
		if(!is_int($this->cropH)){
			switch($this->cropH){
				case 'l':
					$cropX = 0;
					break;
				case 'c':
					if(!$newW){
						if($this->sourceW > $this->sourceH){
							$cropX = floor(($this->sourceW-$this->sourceH)/2);
						}else{
							$cropX = 0;
						}
					}else{
						$cropX = floor(($this->sourceW-$this->destW*$ratio)/2);
					}
					break;
				case 'r':
					if(!$newW){
						if($this->sourceW > $this->sourceH){
							$cropX = floor(($this->sourceW-$this->sourceH));
						}else{
							$cropX = 0;
						}
					}else{
						$cropX = floor($this->sourceW-$this->destW*$ratio);
					}
					break;
				default:
					if($this->sourceW > $this->sourceH){
						$cropX = floor(($this->sourceW-$this->sourceH)/2);
					}else{
						$cropX = 0;
					}
			}
		}else{
			$cropX = $this->cropH;
		}
		return array($cropX, $cropY);
	}
	private function createImageFromFile($src){
		$ext = $this->getImageExt($src);
		switch($ext){
			case 'jpg':
			case 'jpeg':
				return imagecreatefromjpeg($src);
				break;
			case 'png';
				return imagecreatefrompng($src);
				break;
			case 'gif':
				return imagecreatefromgif($src);
				break;
		}
	}
	private function getImageExt($src){
		return strtolower(substr($src,strrpos($src, '.')+1));
	}
	private function getImageDimensions($src){
		$type = gettype($src);
		if($type == 'string' && $arr = @getimagesize($src)){
			return $arr;			
		}
		else{
			throw new Exception('This is not an image: '.htmlspecialchars($src));
		}
	}
	private function errorHandler($e){
		echo '<b>Error ::</b> '.$e->getMessage().'<br />';
		exit();
	}
	
	
	private $allowedImageTypes = array('jpg','jpeg','png','gif');
	private $sourceImageFile;
	private $sourceI;
	private $sourceIExt;
	private $sourceW;
	private $sourceH;
	private $destI;
	private $destIType;
	private $destW;
	private $destH;
	private $cropL;
	private $cropT;
	private $cropH = 'c';
	private $cropV = 't';
	private $quality = 90;
	private $wmImageFile;
	private $wmI;
	private $waterMarkedImage;
	private $wmWidth;
	private $wmHeight;
	private $wmH = 'r';
	private $wmV = 'b';
	private $wmMarginTop = 0;
	private $wmMarginRight = 0;
	private $wmMarginBottom = 0;
	private $wmMarginLeft = 0;
	private $wmAlphaLevel = 1;
	private $cacheDir;
	private $cacheTime = 0;
}
?>