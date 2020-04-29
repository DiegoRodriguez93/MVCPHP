<?php
	include('../models/AJAX.php');

	/**
	 * Clase encargada de los controles previos a manejar cualquier dato con la base de datos, todo procedimiento de BBDD es necesario que pase previamente por esta clase para evitar conflictos.
	 */
	class cAJAX
	{
		/**
		 * @var object	$ajax 				Objeto de la clase AJAX
		 * @var array	$datosSinProcesar	Guarda los datos traídos desde el model para procesarlos y enviarlos a JS
		 * @var array	$respuesta 			Envía a JS el resultado.
		 */
		private $ajax;
		private $datosSinProcesar;
		private $respuesta;

		/**
		 * El único método que se debe llamar, éste se encarga de gestionar el procedimiento.
		 *
		 * @param	string		$funcion	Nombre del método de esta clase a ejecutar.
		 * @param	null|array	$parametros	PARÁMETRO OPCIONAL REQUERIDO EN ALGUNOS MÉTODOS.
		 * 
		 * @return	mixed[]					Retorna un array con la respuesta para que JS proceda a modificar el frontend.
		 */
		public function __construct($funcion, $parametros = array())
		{
			$this->ajax = new AJAX();
			$this->{$funcion}($parametros);
		}


		/**
		 * Controles previos para iniciar sesión, parámetros obligatorios: 'usuario' y 'password'
		 *
		 * @param array $parametros Array que obligatoriamente tiene que traer los elementos 'usuario' y 'password'
		 *
		 * @return array
		 */
		private function logIn($parametros)
		{
			if((int)count($parametros) === 2 && isset($parametros['usuario']) && isset($parametros['password']))
			{
				$this->respuesta = $this->ajax->logIn($parametros['usuario'], $parametros['password']);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> true,
					'mensaje'				=> 'Ha ocurrido un error.'
				);
		}
		/**
		 * Método para buscar cédulas en el padrón y devuelve el nombre y el teléfono correspondiente
		 *
		 * @param   array  $parametros  Únicamente debe traer consigo el indice 'cedula'
		 *
		 * @return  array               [return description]
		 */
		private function buscarCedula($parametros)
		{
			if((int)count($parametros) === 1)
			{
				$this->datosSinProcesar = $this->ajax->buscarCedula($parametros['cedula']);
				if(isset($this->datosSinProcesar->correcto))
				{
					$nombre = ucwords(mb_strtolower($this->datosSinProcesar['nombre']));
					$telefono = (!is_null($this->datosSinProcesar['telefono']) && (int)$this->datosSinProcesar['telefono'] !== 0)
						? $this->datosSinProcesar['telefono']
						: null;

					if(!is_null($telefono))
						$telefono = str_replace('  ', ' ', $telefono);

					$this->respuesta = array
					(
						'correcto'	=> true,
						'nombre'	=> $nombre,
						'telefono'	=> $telefono
					);
				}
				else
					$this->respuesta = $this->datosSinProcesar;
			}
			else
				$this->respuesta = array
				(
					'error' => true,
					'cantidadDeParametros' => true,
					'mensaje' => 'Ha ocurrido un error.'
				);
		}

				/**
		 * Método para buscar cédulas en el padrón y devuelve el nombre y el teléfono correspondiente
		 *
		 * @param   array  $parametros  Únicamente debe traer consigo el indice 'cedula'
		 *
		 * @return  array               [return description]
		 */
		private function buscarNYA($parametros)
		{
			if((int)count($parametros) === 1)
			{
				$this->datosSinProcesar = $this->ajax->buscarNYA($parametros['NYA']);
				if(isset($this->datosSinProcesar->correcto))
				{
					$nombre = ucwords(mb_strtolower($this->datosSinProcesar['nombre']));
					$cedula = ucwords(mb_strtolower($this->datosSinProcesar['cedula']));
					$telefono = (!is_null($this->datosSinProcesar['telefono']) && (int)$this->datosSinProcesar['telefono'] !== 0)
						? $this->datosSinProcesar['telefono']
						: null;

					if(!is_null($telefono))
						$telefono = str_replace('  ', ' ', $telefono);

					$this->respuesta = array
					(
						'correcto'	=> true,
						'cedula'	=> $cedula,
						'nombre'	=> $nombre,
						'telefono'	=> $telefono
					);
				}
				else
					$this->respuesta = $this->datosSinProcesar;
			}
			else
				$this->respuesta = array
				(
					'error' => true,
					'cantidadDeParametros' => true,
					'mensaje' => 'Ha ocurrido un error.'
				);
		}

		/**
		 * Devuelve los lugares de internación habilitados
		 *
		 * @return  array	
		 */
		private function llenarLugaresDeInternacion()
		{
			$this->respuesta = $this->ajax->llenarLugaresDeInternacion();
		}

				/**
		 * Devuelve los departamentos //HECHO POR DIEGO
		 *
		 * @return  array	
		 */
		private function llenarDepartamentos()
		{
			$this->respuesta = $this->ajax->llenarDepartamentos();
		}


		/**
		 * Devuelve los servicios contratados actualmente por la persona
		 *
		 * @param   array $parametros  Únicamente debe traer consigo el indice 'cedula'
		 *
		 * @return  array
		 */
		private function serviciosDelCliente($parametros)
		{
			if(is_numeric($parametros['cedula']) && count($parametros) === 1)
				$this->respuesta = $this->ajax->serviciosDelCliente($parametros['cedula']);
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['cedula']))
				);
		}

		/**
		 * Muestra los productos habilitados actualmente
		 *
		 * @param   array $parametros  Únicamente debe traer consigo el indice 'cedula'
		 *
		 * @return  array
		 */
		private function mostrarProductos($parametros)
		{
			if(is_numeric($parametros['cedula']) && count($parametros) === 1)
				$this->respuesta = $this->ajax->mostrarProductos($parametros['cedula']);
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['cedula']))
				);
		}

		/**
		 * Control para traer los últimos meses de cobranza de la persona
		 *
		 * @param   array $parametros  Únicamente debe traer consigo el indice 'cedula'
		 *
		 * @return  array
		 */
		private function mostrarCobranza($parametros)
		{
			if(is_numeric($parametros['cedula']) && count($parametros) === 1)
			{
				$this->datosSinProcesar = $this->ajax->mostrarCobranza($parametros['cedula']);
				foreach ($this->datosSinProcesar as &$key)
				{
					if((int)strlen($key['fecha']) === 6)
					{
						$nuevoFormato		= explode('/', $key['fecha']);
						$nuevoFormato[1]	= '0'. $nuevoFormato[1];
						$key['fecha']		= $nuevoFormato[0]. '/'. $nuevoFormato[1];
					}
				}

				$this->respuesta = $this->datosSinProcesar;
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['cedula']))
				);
		}

		/**
		 * Muestra los servicios de la persona solicitada
		 *
		 * @param   array $parametros  Únicamente debe traer consigo el indice 'cedula'
		 *
		 * @return  array
		 */
		private function mostrarServicios($parametros)
		{
			if(is_numeric($parametros['cedula']) && count($parametros) === 1)
				$this->respuesta = $this->ajax->mostrarServicios($parametros['cedula']);
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['cedula']))
				);
		}

		/**
		 * Llama al método mostrarDatosBarraCoordinacion de AJAX
		 *
		 * @return  array
		 */
		private function mostrarDatosBarraCoordinacion()
		{
			$this->respuesta = $this->ajax->mostrarDatosBarraCoordinacion();
		}

		/**
		 * Llama al método coordinacionDeServiciosModificarDatos de AJAX
		 *
		 * @return  array
		 */
		private function coordinacionDeServiciosModificarDatos()
		{
			$this->respuesta = $this->ajax->coordinacionDeServiciosModificarDatos();
		}

		/**
		 * Trae todos los servicios coordinados del día seleccionado
		 *
		 * @param   array Únicamente debe traer consigo el índice 'dia'
		 *
		 * @return  array
		 */
		private function coordinacionDeServiciosPendientes($parametros)
		{
			if(count($parametros) === 1)
			{
				$dia				= $parametros['dia'];
				$this->respuesta	= $this->ajax->coordinacionDeServiciosPendientes($dia);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['cedula']))
				);
		}

		/**
		 * Trae todos los servicios activos del tipo seleccionado
		 *
		 * @param   array $parametros Únicamente debe traer consigo el ínidice 'tipo'
		 *
		 * @return  array
		 */
		private function coordinacionDeServiciosActivos($parametros)
		{
			if(count($parametros) === 1)
			{
				$tipo				= $parametros['tipo'];
				$this->respuesta	= $this->ajax->coordinacionDeServiciosActivos($tipo);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['cedula']))
				);
		}

		/**
		 * Trae la información del acompañante indicado
		 *
		 * @param   array $parametros  Únicamente debe traer consigo el indice 'cedula'
		 *
		 * @return  array
		 */
		private function traerInfoAcompanhante($parametros)
		{
			if(count($parametros) === 1)
			{
				$cedula				= $parametros['cedula'];
				$this->respuesta	= $this->ajax->traerInfoAcompanhante($cedula);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['cedula']))
				);
		}

		/**
		 * Trae la información del AFILIADO (Mira, no puse cliente) indicado
		 *
		 * @param   array $parametros  Únicamente debe traer consigo el indice 'cedula'
		 *
		 * @return  array
		 */
		private function traerInfoCliente($parametros)
		{
			if(count($parametros) === 1)
			{
				$cedula				= $parametros['cedula'];
				$this->respuesta	= $this->ajax->traerInfoCliente($cedula);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['cedula']))
				);
		}

		/**
		 * Trae todos los servicios coordinados del acompañante indicado
		 *
		 * @param   array $parametros  Únicamente debe traer consigo el indice 'id'
		 *
		 * @return  array
		 */
		private function mostrarCoordinacionDeServiciosAsignarAcompanhante($parametros)
		{
			if(count($parametros) === 1)
			{
				$id				= $parametros['id'];
				$this->respuesta	= $this->ajax->mostrarCoordinacionDeServiciosAsignarAcompanhante($id);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['id']))
				);
		}

		/**
		 * Le asigna un acompañanete a un servicio indicado
		 *
		 * @param   array $parametros  Únicamente debe traer consigo el indice 'id'
		 *
		 * @return  array
		 */
		private function insertarCoordinacionDeServiciosAsignarAcompanhante($parametros)
		{
			if(count($parametros) === 1)
			{
				$id					= $parametros['id'];
				$this->respuesta	= $this->ajax->insertarCoordinacionDeServiciosAsignarAcompanhante($id);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['id']))
				);
		}

		/**
		 * Trae información de un servicio específico
		 *
		 * @param   array $parametros  Únicamente debe traer consigo el indice 'id'
		 *
		 * @return  array
		 */
		private function informacionDeServicio($parametros)
		{
			if(count($parametros) === 1)
			{
				$id					= $parametros['id'];
				$this->respuesta	= $this->ajax->informacionDeServicio($id);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['id']))
				);
		}

		/**
		 * Trae consigo posibles reintegros
		 *
		 * @param   array $parametros  Únicamente debe traer consigo el indice 'cedula'
		 *
		 * @return  array
		 */
		private function posiblesReintegros($parametros)
		{
			if(count($parametros) === 1)
			{
				$cedula				= $parametros['cedula'];
				$this->respuesta	= $this->ajax->posiblesReintegros($cedula);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['id']))
				);
		}

		/**
		 *
		 *
		 * @param   array $parametros  Únicamente debe traer consigo el indice 'id'
		 *
		 * @return  array
		 */
		private function formularioReintegro($parametros)
		{
			if(count($parametros) === 1)
			{
				$id				= $parametros['id'];
				$this->respuesta	= $this->ajax->formularioReintegro($id);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> (count($parametros) !== 1),
					'parametroMalFormado'	=> (is_nan($parametros['id']))
				);
		}

		/**
		 * Trae la información de todos los usuarios con servicios
		 *
		 * @return array
		 */
		private function mostrarTodosLosUsuariosConServicios()
		{
			$this->respuesta = $this->ajax->mostrarTodosLosUsuariosConServicios();
		}

		/**
		 * Función de control para el toma pedido.
		 *
		 * @param   array $parametros Debe traer consigo 13 indices: nombre, cedula, telefono, lugarInternacion, departamento, servicio, observaciones, sala, cama, piso, horasDeServicio, fechaInicio y horaInicio
		 *
		 * @return  array
		 */
		private function tomarPedido($parametros)
		{
			if(count($parametros) === 13)
			{
				$parametros['fechaInicio'] = explode('/', $parametros['fechaInicio']);
				$parametros['fechaInicio'] = $parametros['fechaInicio'][2]. '-'. $parametros['fechaInicio'][1]. '-'. $parametros['fechaInicio'][0];
				$parametros['fechaInicio'] = new DateTime($parametros['fechaInicio']);

				$this->respuesta = $this->ajax->tomarPedido($parametros['nombre'], $parametros['cedula'], $parametros['telefono'], $parametros['lugarInternacion'], $parametros['departamento'], $parametros['servicio'], $parametros['observaciones'], $parametros['sala'], $parametros['cama'], $parametros['piso'], $parametros['horasDeServicio'], $parametros['fechaInicio']->format('Y-m-d'), $parametros['horaInicio']);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> true
				);
		}

		/**
		 * Función de control para modificar un pedido existente
		 *
		 * @param   array $parametros Debe traer consigo 11 indices: id, telefono, lugarInternacion, servicio, observaciones, sala, cama, piso, horasDeServicio, fechaInicio, horaInicio
		 *
		 * @return  array
		 */
		private function coordinacionDeServiciosModificarDatosFinal($parametros)
		{
			if(count($parametros) === 11)
			{
				$parametros['fechaInicio'] = explode('/', $parametros['fechaInicio']);
				$parametros['fechaInicio'] = $parametros['fechaInicio'][2]. '-'. $parametros['fechaInicio'][1]. '-'. $parametros['fechaInicio'][0];
				$parametros['fechaInicio'] = new DateTime($parametros['fechaInicio']);

				$this->respuesta = $this->ajax->coordinacionDeServiciosModificarDatosFinal($parametros['id'], $parametros['telefono'], $parametros['lugarInternacion'], $parametros['servicio'], $parametros['observaciones'], $parametros['sala'], $parametros['cama'], $parametros['piso'], $parametros['horasDeServicio'], $parametros['fechaInicio'], $parametros['horaInicio']);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> true
				);
		}

		/**
		 * Función de control para modificar un pedido existente
		 *
		 * @param   array $parametros Debe traer consigo 11 indices: id, referencia, fecha, cantidadTurnos, nombreAcompanhanteT1, cedulaAcompanhanteT1, desdeT1, hastaT1, nombreAcompanhanteT2, cedulaAcompanhanteT2, desdeT2, hastaT2, nombreAcompanhanteT3, cedulaAcompanhanteT3, desdeT3 y hastaT3
		 *
		 * @return  array
		 */
		private function guardarCoordinacionDeServiciosAsignarAcompanhante($parametros)
		{
			if(count($parametros) === 16)
			{
				$parametros['fecha'] = explode('/', $parametros['fecha']);
				$parametros['fecha'] = $parametros['fecha'][2]. '-'. $parametros['fecha'][1]. '-'. $parametros['fecha'][0];
				$parametros['fecha'] = new DateTime($parametros['fecha']);

				$this->respuesta = $this->ajax->guardarCoordinacionDeServiciosAsignarAcompanhante($parametros['id'], $parametros['referencia'], $parametros['fecha'], $parametros['cantidadTurnos'], $parametros['nombreAcompanhanteT1'], $parametros['cedulaAcompanhanteT1'], $parametros['desdeT1'], $parametros['hastaT1'], $parametros['nombreAcompanhanteT2'], $parametros['cedulaAcompanhanteT2'], $parametros['desdeT2'], $parametros['hastaT2'], $parametros['nombreAcompanhanteT3'], $parametros['cedulaAcompanhanteT3'], $parametros['desdeT3'], $parametros['hastaT3']);
			}
			else
				$this->respuesta = array
				(
					'error'					=> true,
					'cantidadDeParametros'	=> true
				);
		}

		/**
		 * Se ejecuta al terminar el procedimiento, devuelve un JSON con los datos procesados.
		 *
		 * @return string
		 */
		public function __destruct()
		{
			echo json_encode($this->respuesta);
		}
	}




		/* ------ ------ ------ / ------ ------ ------ / ------ ------ ------ ////// - LÓGICA - ////// ------ ------ ------ / ------ ------ ------ / ------ ------ ------ */

		try
		{
			$parametros = (isset($_POST['parametros']))
				? $_POST['parametros']
				: '';
			$wea = new cAJAX($_POST['funcion'], $parametros);
		}
		catch (\Throwable $th)
		{
			$archivo = explode('\\', $th->getFile());
			$archivo = explode('.', end($archivo));
			$respuesta = array
			(
				'error'		=> true,
				'mensaje'	=> "Ha ocurrido un error crítico, se recargará la página.\nSi vuelve a ver este mensaje por favor llame al interno 509",
				'causa'		=> $th->getMessage(),
				'fichero'	=> $archivo[0],
				'linea'		=> $th->getLine()
			);

			echo json_encode($respuesta);
		}

		/* ------ ------ ------ / ------ ------ ------ / ------ ------ ------ ////// - ////// - ////// ------ ------ ------ / ------ ------ ------ / ------ ------ ------ */