<?php	
	
	class Route {
		static $paramPat 	= "/{[a-z0-9_]+}/";

		static function isMatch($path, $uri) {
			$escPath	= str_replace('/', '\/', $path);
			$pathPat	= "/^" . preg_replace(self::$paramPat, '[\w\d_]+', $escPath) . "$/";

			return preg_match($pathPat, $uri);
		}
		
		function params($path, $uri) {
			$matches	= [];
			$params 	= [];
			preg_match_all(self::$paramPat, $path, $matches);

			foreach ($matches[0] as $value) {
				$paramKey 	= substr($value, 1, -1);
				$keyPos			= strpos($path, $value);
				$paramValue = strtok(substr($uri, $keyPos), '/');
				$path 			= preg_replace("/${value}/", $paramValue, $path);

				$params[$paramKey] = $paramValue;
			}
			
			return $params;
		}

		static function run($path, $uri, $cb) {
			$params = self::params($path, $uri);
			
			$cb($params);
		}

		static function __callStatic($name, $args) {
			$uri		= Request::path();
			$verbs 	= ['get', 'head', 'post', 'patch', 'put', 'delete', 'options'];
			$path 	= $args[0];
			$cb			= $args[1];

			if (self::isMatch($path, $uri) && 
				in_array($name, $verbs) && 
				Request::method() == $name) {
				self::run($path, $uri, $cb);
			}
		}
	}