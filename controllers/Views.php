<?php


	/**
	 * Agrega de forma sencilla y standarizada las rutas tanto para archivos HTML, CSS y JS como archivos de imagenes.
	*/
	class Views
	{
		/* ------ ------ ------ / ------ ------ ------ / ------ ------ ------ ////// - ATRIBUTOS - ////// ------ ------ ------ / ------ ------ ------ / ------ ------ ------ */
			/**
			 * @var string Contiene la versión actual del proyecto, cambiando el valor de acá se cambia en todos lados de forma automática.
			 */
			CONST VERSION = '?v=0.0.1';

		/* ------ ------ ------ / ------ ------ ------ / ------ ------ ------ ////// - ////// - ////// ------ ------ ------ / ------ ------ ------ / ------ ------ ------ */

		/* ------ ------ ------ / ------ ------ ------ / ------ ------ ------ ////// - METODOS - ////// ------ ------ ------ / ------ ------ ------ / ------ ------ ------ */

			/**
			 * Metodo encargado de agregar archivo/s HTML de forma organizada. TODOS LOS ARCHIVOS HTML DEBEN ENCONTRARSE DENTRO DEL DIRECTORIO "views/"
			 * @param array|string $archivo Archivo/s a agregar, puede pasarse un array para agregar más de un archivo al mismo tiempo siempre y cuando pertenezcan al mismo directorio.
			 * @param string|void $carpeta ¡PARÁMETRO OPCIONAL! Carpeta (dentro de views/) donde se encuentra el/los archivo/s HTML.
			 */
			public static function HTML($archivo, $carpeta = '')
			{
				if(is_array($archivo))
				{
					foreach ($archivo as $value)
					{
						if($carpeta === '')
							include('views/'. $value. '.html');
						else
							include('views/'. $carpeta. '/'. $value. '.html');
					}
				}
				else
					if($carpeta === '')
						include('views/'. $archivo. '.html');
					else
						include('views/'. $carpeta. '/'. $archivo. '.html');
			}

			/**
			 * Metodo encargado de agregar archivo/s JS de forma organizada. TODOS LOS ARCHIVOS JS DEBEN ENCONTRARSE DENTRO DEL DIRECTORIO "views/JS"
			 * @param array|string $archivo Archivo/s a agregar, puede pasarse un array para agregar más de un archivo al mismo tiempo siempre y cuando pertenezcan al mismo directorio.
			 * @param string|void $carpeta ¡PARÁMETRO OPCIONAL! Carpeta (dentro de views/JS/) donde se encuentra el/los archivo/s JS.
			 */
			public static function JS($archivo, $carpeta = '')
			{
				if(is_array($archivo))
				{
					foreach ($archivo as $value)
					{
						echo ($carpeta === '')
							? '<script src="views/JS/'. $value.'.js'. self::VERSION. '"></script>'
							: '<script src="views/JS/'. $carpeta.'/'. $value.'.js'. self::VERSION. '"></script>';
					}
				}
				else
					echo ($carpeta === '')
						? '<script src="views/JS/'. $archivo.'.js'. self::VERSION. '"></script>'
						: '<script src="views/JS/'. $carpeta.'/'. $archivo.'.js'. self::VERSION. '"></script>';
			}


			/**
			 * Metodo encargado de agregar archivo/s CSS de forma organizada. TODOS LOS ARCHIVOS CSS DEBEN ENCONTRARSE DENTRO DEL DIRECTORIO "views/CSS"
			 * @param array|string $archivo Archivo/s a agregar, puede pasarse un array para agregar más de un archivo al mismo tiempo siempre y cuando pertenezcan al mismo directorio.
			 * @param string|void $carpeta ¡PARÁMETRO OPCIONAL! Carpeta (dentro de views/CSS/) donde se encuentra el/los archivo/s CSS.
			 */
			public static function CSS($archivo, $carpeta = '')
			{
				if(is_array($archivo))
				{
					echo ($carpeta === '')
						? '<link rel="stylesheet" href="views/CSS/'. $value.'.css'. self::VERSION. '">'
						: '<link rel="stylesheet" href="views/CSS/'. $carpeta.'/'. $value.'.css'. self::VERSION. '">';
				}
				else
					echo ($carpeta === '')
						? '<link rel="stylesheet" href="views/CSS/'. $archivo.'.css'. self::VERSION. '">'
						: '<link rel="stylesheet" href="views/CSS/'. $carpeta.'/'. $archivo.'.css'. self::VERSION. '">';
			}


			/**
			 * Metodo encargado de devolver la ruta de una imagen. TODOS LAS IMAGENES DEBEN ENCONTRARSE DENTRO DEL DIRECTORIO "views/images"
			 * @param array|string $archivo Imagen/es a agregar, puede pasarse un array para agregar más de una imagen al mismo tiempo siempre y cuando pertenezcan al mismo directorio.
			 * @param string|void $carpeta ¡PARÁMETRO OPCIONAL! Carpeta (dentro de views/images/) donde se encuentra/n la/s imagen/es.
			 */
			public static function img($archivo, $carpeta = '')
			{
				if(is_array($archivo))
				{
					foreach ($archivo as $value)
					{
						echo ($carpeta === '')
							? 'views/images/'. $value
							: 'views/images/'. $carpeta. '/'. $value;
					}
				}
				else
					echo ($carpeta === '')
						? 'views/images/'. $archivo
						: 'views/images/'. $carpeta. '/'. $archivo;
			}

		/* ------ ------ ------ / ------ ------ ------ / ------ ------ ------ ////// - ////// - ////// ------ ------ ------ / ------ ------ ------ / ------ ------ ------ */
	}