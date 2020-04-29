<?php
	require_once('Conexion.php');

	class AJAX extends Conexion
	{
		private $mysqli;
		private $q;
		private $r;
		private $r2;
		private $f;
		private $parametros;
		private $respuesta;

		public function __construct()
		{
			$this->mysqli = parent::localhost();
			session_start();
		}

		/**
		 * logIn
		 *
		 * @param  string $usuario
		 * @param  string $password
		 *
		 * @return array
		 */
		public function logIn($usuario, $password)
		{
			$this->q =
			'SELECT
				`usuario`, `pass`, `nivel`, `area`, `grupo`
			FROM
				`usuario`
			WHERE
				`usuario`	= ? AND
				`pass`		= ?';

			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('ss', $usuario, $password);
			$this->r->execute();
			$this->r = $this->r->get_result();

			if((int)$this->r->num_rows !== 0)
			{
				$this->f = $this->r->fetch_assoc();

				$_SESSION = array
				(
					'validada'	=> true,
					'usuario'	=> $this->f['usuario'],
					'nivel'		=> $this->f['nivel'],
					'area'		=> $this->f['area'],
					'grupo'		=> $this->f['grupo'],
					'permiso'	=> 5
				);

				$this->respuesta = array
				(
					'correcto' => true
				);
			}
			else
				$this->respuesta = array
				(
					'error'			=> true,
					'incorrecto'	=> true
				);

			return $this->respuesta;
		}

		/**
		 * buscarCedula
		 *
		 * @param  int $cedula
		 *
		 * @return array
		 */
		public function buscarCedula($cedula)
		{
			$this->q =
			'SELECT
				`id`, `pendiente`, `restanhrs`, `activo`
			FROM
				`pedido_acomp`
			WHERE
				`cancelado`		= 0 AND
				`finalizado`	= 0 AND
				`id_socio`		= ?
			ORDER BY
				`id` DESC
			LIMIT
				1';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $cedula);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				$this->f = $this->r->fetch_assoc();
				if($this->r->num_rows === 0)
				{
					$this->mysqli = parent::padron();
					$this->q =
					'SELECT
						`nombre`, `telefono_titular`
					FROM
						`padron_datos_socio`
					WHERE
						`cedula` = ?';
					$this->r = $this->mysqli->prepare($this->q);
					$this->r->bind_param('s', $cedula);
					if($this->r->execute())
					{
						$this->r = $this->r->get_result();
						if((int)$this->r->num_rows !== 0)
						{
							$this->f = $this->r->fetch_assoc();

							$this->respuesta = array
							(
								'correcto'	=> (bool)	true,
								'nombre'	=> (string)	ucwords(mb_strtolower($this->f['nombre'])),
								'telefono'	=> (string)	$this->f['telefono_titular']
							);
						}
						else
							$this->respuesta = array
							(
								'noSocio'	=> (bool)	true
							);
					}
					else
						$this->respuesta = array
						(
							'error'		=> (bool)	true,
							'query'		=> (bool)	true,
							'nroQuery'	=> (int)	2
						);
				}
				else
					$this->respuesta = array
					(
						'sinFinalizar'		=> (bool)	true,
						'sinAcompanhante'	=> (bool)	((int)$this->f['pendiente'] === 1),
						'pendiente'			=> (bool)	((int)$this->f['restanhrs'] > 0 && (int)$this->f['activo'] === 1),
						'completo'			=> (bool)	((int)$this->f['restanhrs'] === 0 && (int)$this->f['activo'] === 1),
						'id'				=> (int)	$this->f['id']
					);
			}
			else
				$this->respuesta = array
				(
					'error'		=> (bool)	true,
					'query'		=> (bool)	true,
					'nroQuery'	=> (int)	1
				);

			return $this->respuesta;
		}

				/**
		 * buscarCedula ESCRITO POR DIEGO
		 *
		 * @param  string $nya
		 *
		 * @return array
		 */
		public function buscarNYA($nya)
		{
			$nombresyapellidos = explode(" ", $nya);

			$whereParaQuery = '';

			for ($i = 0; $i <= count($nombresyapellidos) - 1; $i++) {
				if($i == 0){
					$where = ' WHERE ';
				}else{
					$where = '';
				}
				
				if($i ==  count($nombresyapellidos) - 1){
					$and = '';
				}else{
					$and = ' AND ';
				}
				
				$whereParaQuery .= $where . " nombre LIKE '%" . $nombresyapellidos[$i] . "%' " . $and ;
			}


					$this->mysqli = parent::padron();
					$this->q =
					"SELECT
						`nombre`, `telefono_titular`,`cedula`
					FROM
						`padron_datos_socio`
						$whereParaQuery LIMIT 16";

					$this->r = $this->mysqli->prepare($this->q);

					if($this->r->execute())
					{
						$this->r = $this->r->get_result();
						if((int)$this->r->num_rows !== 0)
						{
							while($this->f = $this->r->fetch_assoc())
							{
			
								$this->respuesta['datos'][] = array
								(
									'correcto'	=> (bool)	true,
									'cedula'	=> (int) $this->f['cedula'],
									'nombre'	=> (string)	ucwords(mb_strtolower($this->f['nombre'])),
									'telefono'	=> (string)	$this->f['telefono_titular']
								);
							}
							/*							
							$this->respuesta = array
							(
								'correcto'	=> (bool)	true,
								'nombre'	=> (string)	ucwords(mb_strtolower($this->f['nombre'])),
								'telefono'	=> (string)	$this->f['telefono_titular']
							);}*/
						}
						else
							$this->respuesta = array
							(
								'noSocio'	=> (bool)	true
							);
					}
					else
						$this->respuesta = array
						(
							'error'		=> (bool)	true,
							'query'		=> (bool)	true,
							'nroQuery'	=> (int)	2
						);
	

			return $this->respuesta;
		}

		/* ------ ------ ------ / ------ ------ ------ / ------ ------ ------ ////// - ////// - ////// ------ ------ ------ / ------ ------ ------ / ------ ------ ------ */


		/**
		 * llenarLugaresDeInternacion
		 *
		 * @return array
		 */
		public function llenarLugaresDeInternacion()
		{
			$this->q =
			'SELECT
				`id`, `nombre`
			FROM
				`lugar`
			ORDER BY
				`nombre`';
			if($this->r = $this->mysqli->query($this->q))
			{
				$this->respuesta = array
				(
					'correcto' => true
				);
				while($this->f = $this->r->fetch_assoc())
				{
					if(is_null($this->f['nombre']))
						continue;

					$this->f['nombre'] = (str_replace('_', ' ', $this->f['nombre']));

					$this->respuesta['datos'][] = array
					(
						'id'		=> (int)	$this->f['id'],
						'nombre'	=> (string)	ucwords(mb_strtolower($this->f['nombre']))
					);
				}
			}
			else
				$this->respuesta = array
				(
					'error' => true
				);

				return $this->respuesta;
		}

		
		/**
		 * llenarDepartamentos
		 *
		 * @return array
		 */
		public function llenarDepartamentos()
		{
			$this->q ="SELECT localidad FROM `pedido_acomp` WHERE localidad != '' GROUP BY localidad";

			if($this->r = $this->mysqli->query($this->q))
			{
				$this->respuesta = array
				(
					'correcto' => true
				);
				while($this->f = $this->r->fetch_assoc())
				{
					if(is_null($this->f['localidad']))
						continue;

					$this->respuesta['datos'][] = array
					(
						'id'		=> (string)	$this->f['localidad'],
						'nombre'	=> (string)	$this->f['localidad']
					);
				}
			}
			else
				$this->respuesta = array
				(
					'error' => true
				);

				return $this->respuesta;
		}

		/**
		 * serviciosDelCliente
		 *
		 * @param  int $cedula
		 *
		 * @return array
		 */
		public function serviciosDelCliente($cedula)
		{
			$this->mysqli = parent::padron();
			$this->q =
			'SELECT
			(
				CASE
					WHEN(`p`.`servicio` > "03" AND `p`.`servicio` != "06" AND `p`.`servicio` != "07")
						THEN "01"
						ELSE `p`.`servicio`
					END
			) AS `sanatorio`, SUM(`p`.`hora`) AS `hora`, `s`.`servicio`
			FROM
				`padron_producto_socio` AS `p`
			INNER JOIN
				`servicios_codigos` AS `s`
					ON
					`p`.`servicio` = `s`.`nro_servicio`
			WHERE
				`p`.`cedula` = ?
			GROUP BY
				`sanatorio`';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $cedula);

			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				$this->respuesta = array
				(
					'correcto' => true
				);

				while($this->f = $this->r->fetch_assoc())
				{
					$this->respuesta['datos'][] = array
					(
						'id'		=> (string)	$this->f['hora'],
						'nombre'	=> (string)	ucwords(mb_strtolower($this->f['servicio']))
					);
				}
			}
			else
				$this->respuesta = array
				(
					'error' => true
				);

			return $this->respuesta;
		}

		/**
		 * mostrarProductos
		 *
		 * @param  int $cedula
		 *
		 * @return array
		 */
		public function mostrarProductos($cedula)
		{
			$this->mysqli = parent::padron();

			$this->q =
			'SELECT
				`p`.`servicio` AS `id`, `s`.`servicio`, SUM(`p`.`hora`) AS `horas`, SUM(`p`.`importe`) AS `importe`, `p`.`fecha_afiliacion`
			FROM
				`padron_producto_socio` AS `p`
			INNER JOIN
				`servicios_codigos` AS `s` ON
					`p`.`servicio` = `s`.`nro_servicio`
			WHERE
				`p`.`cedula` = ?
			GROUP BY
				`cedula`, `servicio`, `fecha_afiliacion`';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $cedula);

			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				while($this->f = $this->r->fetch_assoc())
				{
					$this->f['fecha_afiliacion'] = new DateTime($this->f['fecha_afiliacion']);
					$this->respuesta[] = array
					(
						'id'				=> (int)	$this->f['id'],
						'servicio'			=> (string)	$this->f['servicio'],
						'horas'				=> (int)	$this->f['horas'],
						'importe'			=> (int)	$this->f['importe'],
						'fecha_afiliacion'	=> (string)	$this->f['fecha_afiliacion']->format('d/m/Y')
					);
				}
			}
			else
				$this->respuesta = array
				(
					'error' => true
				);

			return $this->respuesta;
		}

		/**
		 * mostrarCobranza
		 *
		 * @param  int $cedula
		 *
		 * @return array
		 */
		public function mostrarCobranza($cedula)
		{
			$this->mysqli = parent::cobranza();

			$this->q =
			'SELECT
				`MES`, `ANO`, `IMPORTE`
			FROM
				`cobrado`
			WHERE
				`CEDULA` = ?
			ORDER BY
				`ANO` DESC,
				CONVERT(SUBSTRING(`MES`, 1), UNSIGNED INT) DESC
			LIMIT
				6';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $cedula);

			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				while($this->f = $this->r->fetch_assoc())
					$this->respuesta[] = array
					(
						'fecha'		=> (string)	$this->f['ANO']. '/'. $this->f['MES'],
						'importe'	=> (int)	$this->f['IMPORTE']
					);
			}

			return $this->respuesta;
		}

		/**
		 * mostrarServicios
		 *
		 * @param  int $cedula
		 *
		 * @return array
		 */
		public function mostrarServicios($cedula)
		{
			$this->q =
			'SELECT
				`s`.`idinfo`, MIN(`s`.`diat1`) AS `desde`, MAX(`s`.`diat1`) AS `hasta`, `s`.`turnos`
			FROM
				`servicios` AS `s`
			INNER JOIN
				`pedido_acomp` AS `p`
				ON
				`s`.`idinfo`	= `p`.`id`
			WHERE
				`p`.`id_socio`	= ?
			GROUP BY
				`s`.`idinfo`
			ORDER BY
				`diat1` DESC';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $cedula);

			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				if($this->r->num_rows > 0)
					while($this->f = $this->r->fetch_assoc())
					{
						$this->f['desde'] = new DateTime($this->f['desde']);
						$this->f['hasta'] = new DateTime($this->f['hasta']);
						$this->respuesta[] = array
						(
							'id'		=> (int)	$this->f['idinfo'],
							'desde'		=> (string)	$this->f['desde']->format('d/m/Y'),
							'hasta'		=> (string)	$this->f['hasta']->format('d/m/Y'),
							'cantidad'	=> (int)	$this->f['hasta']->diff($this->f['desde'])->d,
							'turnos'	=> (int)	$this->f['turnos']
						);
					}
				else
					$this->respuesta = array
					(
						'sinRegistros' => true
					);
			}

			return $this->respuesta;
		}

		/**
		 * mostrarDatosBarraCoordinacion
		 *
		 * @return array
		 */
		public function mostrarDatosBarraCoordinacion()
		{
			$fecha = new DateTime();
			$this->q =
			'SELECT
				COUNT(`id`) AS `cantidad`
			FROM
				`pedido_acomp`
			WHERE
				`pendiente`	= 1 AND
				`grupo`		= ?';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $_SESSION['grupo']);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				$this->f = $this->r->fetch_assoc();
				$this->respuesta['badgeServiciosSinAcompanhante'] = $this->f['cantidad'];
				$this->q =
				'SELECT
					COUNT(`id`) AS `cantidad`
				FROM
					`pedido_acomp`
				WHERE
					`restanhrs`		= 0 AND
					`activo`		= 1 AND
					`grupo`			= ?';
				$this->r = $this->mysqli->prepare($this->q);
				$this->r->bind_param('i', $_SESSION['grupo']);
				if($this->r->execute())
				{/* SAQUE DE LAS QUERY /// AND	`finalizado`	= 0 AND */
					$this->r = $this->r->get_result();
					$this->f = $this->r->fetch_assoc();
					$this->respuesta['badgeServiciosActivosCompletos'] = $this->f['cantidad'];
					$this->q =
					'SELECT
						COUNT(`id`) AS `cantidad`
					FROM
						`pedido_acomp`
					WHERE
						`restanhrs`		= 1 AND
						`activo`		= 1 AND
						`grupo`			= ?';
					$this->r = $this->mysqli->prepare($this->q);
					$this->r->bind_param('i', $_SESSION['grupo']);
					if($this->r->execute())
					{
						$this->r = $this->r->get_result();
						$this->f = $this->r->fetch_assoc();
						$this->respuesta['badgeServiciosActivosPendientes'] = $this->f['cantidad'];
						$this->q =
						'SELECT
							COUNT(`id`) AS `cantidad`
						FROM
							`servicios`
						where
							`hcompleta`	= 1 AND
							`grupo`		= ? AND
							`diat1`		= ? AND
							`activo`	!= 2';
						$this->r = $this->mysqli->prepare($this->q);
						$this->r->bind_param('is', $_SESSION['grupo'], ($fecha->format('Y-m-d')));
						if($this->r->execute())
						{
							$fecha->modify('-1 days');
							$this->r = $this->r->get_result();
							$this->f = $this->r->fetch_assoc();
							$this->respuesta['badgeTurnosPendientesHoy'] = $this->f['cantidad'];
							$this->q =
							'SELECT
								COUNT(`id`) AS `cantidad`
							FROM
								`servicios`
							WHERE
								`hcompleta`	= 1 AND
								`grupo`		= ? AND
								`diat1`		= ? AND
								`activo`	!= 2';
							$this->r = $this->mysqli->prepare($this->q);
							$this->r->bind_param('is', $_SESSION['grupo'], ($fecha->format('Y-m-d')));
							if($this->r->execute())
							{
								$fecha->modify('+2 days');
								$this->r = $this->r->get_result();
								$this->f = $this->r->fetch_assoc();
								$this->respuesta['badgeTurnosPendientesAyer'] = $this->f['cantidad'];
								$this->q =
								'SELECT
									COUNT(`id`) AS `cantidad`
								FROM
									servicios
								WHERE
									hcompleta	= 1 &&
									grupo		= ? &&
									diat1		= ? &&
									activo		!= 2';
								$this->r = $this->mysqli->prepare($this->q);
								$this->r->bind_param('is', $_SESSION['grupo'], ($fecha->format('Y-m-d')));
								if($this->r->execute())
								{
									$this->r = $this->r->get_result();
									$this->f = $this->r->fetch_assoc();
									$this->respuesta['badgeTurnosPendientesManhana'] = $this->f['cantidad'];
									$this->respuesta['correcto'] = true;
								}
								else
									$this->respuesta = array
									(
										'error'		=> (bool)	true,
										'query'		=> (bool)	true,
										'nroQuery'	=> (int)	6
									);
							}
							else
								$this->respuesta = array
								(
									'error'		=> (bool)	true,
									'query'		=> (bool)	true,
									'nroQuery'	=> (int)	5
								);
						}
						else
							$this->respuesta = array
							(
								'error'		=> (bool)	true,
								'query'		=> (bool)	true,
								'nroQuery'	=> (int)	4
							);
					}
					else
						$this->respuesta = array
						(
							'error'		=> (bool)	true,
							'query'		=> (bool)	true,
							'nroQuery'	=> (int)	3
						);
				}
				else
					$this->respuesta = array
					(
						'error'		=> (bool)	true,
						'query'		=> (bool)	true,
						'nroQuery'	=> (int)	2
					);
			}
			else
				$this->respuesta = array
				(
					'error'		=> (bool)	true,
					'query'		=> (bool)	true,
					'nroQuery'	=> (int)	1
				);

			return $this->respuesta;
		}

		/**
		 * coordinacionDeServiciosModificarDatos
		 *
		 * @return array
		 */
		public function coordinacionDeServiciosModificarDatos()
		{
			$this->q =
			'SELECT
				`id`, `id_socio`, `nombre_socio`, `obs_socio`, `fecha_ini`, `hs_ini`, `hs_x_dia`, `telefono`, `lugar`, `piso`, `sala`, `cama`, `tipo`
			FROM
				`pedido_acomp`
			WHERE
				`grupo` = ?
			ORDER BY
				`id` DESC
			LIMIT
				150';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $_SESSION['grupo']);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				if($this->r->num_rows !== 0)
				{
					while($this->f = $this->r->fetch_assoc())
					{
						$this->f['fecha_ini'] = new DateTime($this->f['fecha_ini']);
						$this->respuesta[] = array
						(
							'id'			=> (int)	$this->f['id'],
							'cedula'		=> (int)	$this->f['id_socio'],
							'nombre'		=> (string)	ucwords(mb_strtolower($this->f['nombre_socio'])),
							'observacion'	=> (string)	str_replace('"', '\'', $this->f['obs_socio']),
							'fecha'			=> (string)	$this->f['fecha_ini']->format('d/m/y'),
							'hora'			=> (string)	$this->f['hs_ini'],
							'horaPorDia'	=> (int)	$this->f['hs_x_dia'],
							'telefono'		=> (string)	$this->f['telefono'],
							'lugar'			=> (string)	str_replace('_', ' ', $this->f['lugar']),
							'piso'			=> (int)	$this->f['piso'],
							'sala'			=> (string)	$this->f['sala'],
							'cama'			=> (int)	$this->f['cama'],
							'tipo'			=> (string)	$this->f['tipo']
						);
					}
				}
				else
					$this->respuesta = array
					(
						'sinRegistros' => true
					);
			}
			else
				$this->respuesta = array
				(
					'error' => true
				);

			return $this->respuesta;
		}

		/**
		 * coordinacionDeServiciosPendientes
		 *
		 * @param  int $dia
		 *
		 * @return array
		 */
		public function coordinacionDeServiciosPendientes($dia)
		{
			$fecha = new DateTime();
			if($dia !== '0')
				$fecha->modify($dia. ' days');

			$this->q =
			'SELECT
				`id`, `idinfo`, `turnos`, `diat1`, `nombreacomt1`, `ciacompt1`, `hit1`, `hft1`, `nombreacomt2`, `ciacompt2`, `hit2`, `hft2`, `nombreacomt3`, `ciacompt3`, `hit3`, `hft3`
			FROM
				`servicios`
			WHERE
				`hcompleta`	= 1 AND
				`grupo`		= ? AND
				`diat1`		= ? AND
				`activo`	!= 2';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('is', $_SESSION['grupo'], ($fecha->format('Y-m-d')));
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				if($this->r->num_rows !== 0)
				{
					while($this->f = $this->r->fetch_assoc())
						$this->respuesta[] = array
						(
							'id'				=> (int)	$this->f['id'],
							'idinfo'				=> (int)	$this->f['idinfo'],
							'turnos'			=> (int)	$this->f['turnos'],
							'fecha'				=> (string)	$fecha->format('d/m/y'),
							'nombreT1'			=> (string)	ucwords(mb_strtolower($this->f['nombreacomt1'])),
							'cedulaT1'			=> (int)	$this->f['ciacompt1'],
							'cantidadHorasT1'	=> (int)	0,
							'horaInicioT1'		=> (string)	$this->f['hit1'],
							'horaFinalT1'		=> (string)	$this->f['hft1'],
							'nombreT2'			=> (string)	ucwords(mb_strtolower($this->f['nombreacomt2'])),
							'cedulaT2'			=> (int)	$this->f['ciacompt2'],
							'cantidadHorasT2'	=> (int)	0,
							'horaInicioT2'		=> (string)	$this->f['hit2'],
							'horaFinalT2'		=> (string)	$this->f['hft2'],
							'nombreT3'			=> (string)	ucwords(mb_strtolower($this->f['nombreacomt3'])),
							'cedulaT3'			=> (int)	$this->f['ciacompt3'],
							'cantidadHorasT3'	=> (int)	0,
							'horaInicioT3'		=> (string)	$this->f['hit3'],
							'horaFinalT3'		=> (string)	$this->f['hft3']
						);
				}
				else
					$this->respuesta = array
					(
						'sinRegistros' => true
					);
			}
			else
				$this->respuesta = array
				(
					'error' => true
				);

			return $this->respuesta;
		}

		/**
		 * coordinacionDeServiciosActivos
		 *
		 * @param  string $tipo
		 *
		 * @return array
		 */
		public function coordinacionDeServiciosActivos($tipo)
		{
			if($tipo === 'sinAcompanhante')
			{
				$this->q =
				'SELECT
					`id`, `id_socio`, `nombre_socio`, `obs_socio`, `fecha_ini`, `hs_ini`, `hs_x_dia`, `telefono`, `lugar`, `piso`, `sala`, `cama`, `acompanante`, `fecha_carga`, `tiempopedido`
				FROM
					`pedido_acomp`
				WHERE
					`pendiente`		= 1 AND
					`finalizado`	= 0 AND
					`grupo`			= ?';
			}
			else if($tipo === 'pendiente')
			{
				$this->q =
				'SELECT
					`id`, `id_socio`, `nombre_socio`, `obs_socio`, `fecha_ini`, `hs_ini`, `hs_x_dia`, `telefono`, `lugar`, `piso`, `sala`, `cama`, `acompanante`, `fecha_carga`, `tiempopedido`
				FROM
					`pedido_acomp`
				WHERE
					`restanhrs`		= 1 AND
					`activo`		= 1 AND
					`finalizado`	= 0 AND
					`grupo`			= ?';
			}
			else if($tipo === 'completo')
			{
				$this->q =
				'SELECT
					`id`, `id_socio`, `nombre_socio`, `obs_socio`, `fecha_ini`, `hs_ini`, `hs_x_dia`, `telefono`, `lugar`, `piso`, `sala`, `cama`, `acompanante`, `fecha_carga`, `tiempopedido`
				FROM
					`pedido_acomp`
				WHERE
					`restanhrs`		= 0 AND
					`activo`		= 1 AND
					`finalizado`	= 0 AND
					`grupo`			= ?';
			}

			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $_SESSION['grupo']);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				while($this->f = $this->r->fetch_assoc())
				{
					$fecha				= new DateTime($this->f['fecha_ini']);
					$fechaCarga			= new DateTime($this->f['fecha_carga']);
					$tiempoPedido		= new DateTime($this->f['tiempopedido']);
					$this->respuesta[]	= array
					(
						'id'			=> (int)	$this->f['id'],
						'cedula'		=> (int)	$this->f['id_socio'],
						'nombre'		=> (string)	ucwords(mb_strtolower($this->f['nombre_socio'])),
						'observacion'	=> (string)	$this->f['obs_socio'],
						'fecha'			=> (string)	$fecha->format('d/m/y'),
						'hora'			=> (string)	$this->f['hs_ini'],
						'horas'			=> (int)	$this->f['hs_x_dia'],
						'telefono'		=> (string)	$this->f['telefono'],
						'lugar'			=> (string)	str_replace('_', ' ', $this->f['lugar']),
						'piso'			=> (int)	$this->f['piso'],
						'sala'			=> (string)	$this->f['sala'],
						'cama'			=> (int)	$this->f['cama'],
						//'acompanhante'	=> (string)	$this->f['acompanante'],
						'fechaCarga'	=> (string)	$fechaCarga->format('d/m/y H:s'),
						'tiempoPedido'	=> (string)	$tiempoPedido->format('d/m/y H:s')
					);
				}
			}
			else
				$this->respuesta = array
				(
					'error' => true
				);

			return $this->respuesta;
		}

		/**
		 * traerInfoAcompanhante
		 *
		 * @param  int $cedula
		 *
		 * @return array
		 */
		public function traerInfoAcompanhante($cedula)
		{
			$mssql				= parent::nodum();
			$cedula				= (string) $cedula;
			$fechaActual		= new DateTime();

			$q =
			'SELECT
				t.nombre_completo, t.telefono, t.doc_persona, t.fec_nac, t.fingreso, t.fegreso, d.nom_dpto, t.estado_trab, h.desc_horario, t.direccion
			FROM
				v_RHTrabajador AS t
			INNER JOIN
				ct_dptos AS d
				ON d.cod_dpto = t.cod_dpto
			INNER JOIN
				ct_RHHorarios AS h
				ON h.cod_horario = t.cod_horario
			WHERE
				t.cod_cargo = 501 AND
				doc_persona = ?';
			$r = sqlsrv_prepare($mssql, $q, array(&$cedula));
			sqlsrv_execute($r);
			if(sqlsrv_has_rows($r))
			{
				$f = sqlsrv_fetch_array($r, SQLSRV_FETCH_ASSOC);
				$f['nombre_completo']	= str_replace('  ', '', mb_convert_case($f['nombre_completo'], MB_CASE_TITLE, "UTF-8"));
				$f['nom_dpto'] 			= str_replace('  ', '', mb_convert_case($f['nom_dpto'], MB_CASE_TITLE, "UTF-8"));
				$f['nom_dpto']			= substr($f['nom_dpto'], 0, -1);
				$f['telefono']			= ($f['telefono'] === null)
					? 'Sin teléfono'
					: str_replace('  ', '', $f['telefono']);
				if($f['telefono'][0] == 9)
					$f['telefono'] 		= '0' . $f['telefono'];
				$nomDpto				= explode('-', $f['nom_dpto']);
				if($nomDpto[0] 			== $nomDpto[1])
					$f['nom_dpto'] 		= $nomDpto[0];
				$f['doc_persona'] 		= str_replace(' ', '', mb_convert_case($f['doc_persona'], MB_CASE_TITLE, "UTF-8"));
				$f['fingreso']			= $f['fingreso']->format('d/m/Y');
				$f['fec_nac']			= $f['fec_nac']->format('d/m/Y');
				$f['fegreso']			= ($f['fegreso'] != null)
					? $f['fegreso']->format('d/m/Y')
					: '--/--/----';
				if(is_null($f['direccion']))
					$f['direccion'] = 'Sin registro';

				$this->respuesta	= array
				(
					'nombre'			=> (string)	ucwords(mb_strtolower($f['nombre_completo'])),
					'telefono'			=> (string)	$f['telefono'],
					'departamento'		=> (string) $f['nom_dpto'],
					'direccion'			=> (string) $f['direccion'],
					'nacimiento'		=> (string) $f['fec_nac'],
					'ingreso'			=> (string) $f['fingreso'],
					'enServicio'		=> (bool)	false,
					'ultimoDia'			=> (string)	'1337/03/14',
					'servicioFuturo'	=> (string)	null,
					'horasMes'			=> (int)	0,
					'horasTotales'		=> (int)	0
				);

				$this->q =
				'SELECT
					`id`
				FROM
					`servicios`
				WHERE
					`ciacompt1` = ? ||
					`ciacompt2` = ? ||
					`ciacompt3` = ?';
				$this->r = $this->mysqli->prepare($this->q);
				$this->r->bind_param('sss', $cedula, $cedula, $cedula);
				if($this->r->execute())
				{
					$this->r = $this->r->get_result();
					if($this->r->num_rows !== 0)
					{
						$this->q =
						'SELECT
							`diat1`, `hit1`, `hft1`
						FROM
							`servicios`
						WHERE
							`ciacompt1` = ?';
						$this->r = $this->mysqli->prepare($this->q);
						$this->r->bind_param('s', $cedula);
						if($this->r->execute())
						{
							$this->r = $this->r->get_result();
							while($this->f = $this->r->fetch_assoc())
							{
								$dia		= new DateTime($this->f['diat1']);
								$horaInicio	= (int) explode(':', $this->f['hit1'])[0];
								$horaFinal	= (int) explode(':', $this->f['hft1'])[0];
								$diferencia	= ($horaFinal < $horaInicio)
									? (int) (($horaFinal + 24) - $horaInicio)
									: (int) ($horaFinal - $horaInicio);

								if($fechaActual->format('Y-m-d') === $dia->format('Y-m-d'))
									$this->respuesta['enServicio'] = true;

								if($this->respuesta['ultimoDia'] < $dia->format ('Y-m-d') && $dia->format('Y-m-d') < $fechaActual->format('Y-m-d'))
									$this->respuesta['ultimoDia'] = $dia->format('Y-m-d');

								if($this->respuesta['servicioFuturo'] < $dia->format('Y-m-d') && $dia->format('Y-m-d') > $fechaActual->format('Y-m-d'))
									$this->respuesta['servicioFuturo'] = $dia->format('Y-m-d');

								if($dia->format('m') === $fechaActual->format('m') && $fechaActual->format('Y-m-d') >= $dia->format('Y-m-d'))
									$this->respuesta['horasMes'] += $diferencia;

								if($dia->format('Y-m-d') <= $fechaActual->format('Y-m-d'))
									$this->respuesta['horasTotales'] += $diferencia;
							}

							$this->q =
							'SELECT
								`diat1`, `hit2`, `hft2`
							FROM
								`servicios`
							WHERE
								`ciacompt2` = ?';
							$this->r = $this->mysqli->prepare($this->q);
							$this->r->bind_param('s', $cedula);
							if($this->r->execute())
							{
								$this->r = $this->r->get_result();
								while($this->f = $this->r->fetch_assoc())
								{
									$dia		= new DateTime($this->f['diat1']);
									$horaInicio	= (int) explode(':', $this->f['hit2'])[0];
									$horaFinal	= (int) explode(':', $this->f['hft2'])[0];
									$diferencia	= ($horaFinal < $horaInicio)
										? (int) (($horaFinal + 24) - $horaInicio)
										: (int) ($horaFinal - $horaInicio);

									if($fechaActual->format('Y-m-d') === $dia->format('Y-m-d'))
										$this->respuesta['enServicio'] = true;

									if($this->respuesta['ultimoDia'] < $dia->format('Y-m-d') && $dia->format('Y-m-d') < $fechaActual->format('Y-m-d'))
										$this->respuesta['ultimoDia'] = $dia->format('Y-m-d');

									if($this->respuesta['servicioFuturo'] < $dia->format('Y-m-d') && $dia->format('Y-m-d') > $fechaActual->format('Y-m-d'))
										$this->respuesta['servicioFuturo'] = $dia->format('Y-m-d');

									if($dia->format('m') === $fechaActual->format('m'))
										$this->respuesta['horasMes'] += $diferencia;

									if($dia->format('Y-m-d') <= $fechaActual->format('Y-m-d'))
										$this->respuesta['horasTotales'] += $diferencia;
								}

								$this->q =
								'SELECT
									`diat1`, `hit3`, `hft3`
								FROM
									`servicios`
								WHERE
									`ciacompt3` = ?';
								$this->r = $this->mysqli->prepare($this->q);
								$this->r->bind_param('s', $cedula);
								if($this->r->execute())
								{
									$this->r = $this->r->get_result();
									while($this->f = $this->r->fetch_assoc())
									{
										$dia		= new DateTime($this->f['diat1']);
										$horaInicio	= (int) explode(':', $this->f['hit3'])[0];
										$horaFinal	= (int) explode(':', $this->f['hft3'])[0];
										$diferencia	= ($horaFinal < $horaInicio)
											? (int) (($horaFinal + 24) - $horaInicio)
											: (int) ($horaFinal - $horaInicio);

										if($fechaActual->format('Y-m-d') === $dia->format('Y-m-d'))
											$this->respuesta['enServicio'] = true;

										if($this->respuesta['ultimoDia'] < $dia->format('Y-m-d') && $dia->format('Y-m-d') < $fechaActual->format('Y-m-d'))
											$this->respuesta['ultimoDia'] = $dia->format('Y-m-d');

										if($this->respuesta['servicioFuturo'] < $dia->format('Y-m-d') && $dia->format('Y-m-d') > $fechaActual->format('Y-m-d'))
											$this->respuesta['servicioFuturo'] = $dia->format('Y-m-d');

										if($dia->format('m') === $fechaActual->format('m'))
											$this->respuesta['horasMes'] += $diferencia;

										if($dia->format('Y-m-d') <= $fechaActual->format('Y-m-d'))
											$this->respuesta['horasTotales'] += $diferencia;
									}

									$this->respuesta['ultimoDia']		= new DateTime($this->respuesta['ultimoDia']);
									$this->respuesta['ultimoDia']		= $this->respuesta['ultimoDia']->format('d/m/Y');
									$this->respuesta['servicioFuturo']	= new DateTime($this->respuesta['servicioFuturo']);
									$this->respuesta['servicioFuturo']	= $this->respuesta['servicioFuturo']->format('d/m/Y');
								}
								else
									$this->respuesta = array
									(
										'error'		=> (bool)	true,
										'query'		=> (bool)	true,
										'nroQuery'	=> (int)	4
									);
							}
							else
								$this->respuesta = array
								(
									'error'		=> (bool)	true,
									'query'		=> (bool)	true,
									'nroQuery'	=> (int)	3
								);
						}
						else
							$this->respuesta = array
							(
								'error'		=> (bool)	true,
								'query'		=> (bool)	true,
								'nroQuery'	=> (int)	2
							);
					}
					else
						$this->respuesta = array
						(
							'sinRegistros' => true
						);
				}
				else
					$this->respuesta = array
					(
						'error'		=> (bool)	true,
						'query'		=> (bool)	true,
						'nroQuery'	=> (int)	1
					);
			}
			else
				$this->respuesta = array
				(
					'sinRegistros' => true
				);

			return $this->respuesta;
		}

		/**
		 * traerInfoCliente
		 *
		 * @param  int $cedula
		 *
		 * @return array
		 */
		public function traerInfoCliente($cedula)
		{
			
			$this->q =
			'SELECT
				*
			FROM
				`pedido_acomp`
			WHERE
				`id_socio` = ?';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $cedula);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				$this->respuesta['correcto'] = true;
				if($this->r->num_rows !== 0)
				{
					while($this->f = $this->r->fetch_assoc())
						$this->respuesta['datos'][] = array
						(
							'id'			=> (int)	$this->f['id'],
							'nombreSocio'	=> (string)	$this->f['nombre_socio'],
							'horas'			=> (int)	$this->f['hs_x_dia'],
							'lugar'			=> (string)	$this->f['lugar'],
							'cancelado'		=> (bool)	($this->f['cancelado'] === 1),
							'pendiente'		=> (bool)	($this->f['pendiente'] === 1),
							'activo'		=> (bool)	($this->f['activo'] === 1),
							'finalizado'	=> (bool)	($this->f['finalizado'] === 1)
						);
				}
				else
					$this->respuesta = array
					(
						'sinRegistros' => true
					);
			}
			else
				$this->respuesta = array
				(
					'error'		=> (bool)	true,
					'query'		=> (bool)	true,
					'nroQuery'	=> (int)	1
				);

			return $this->respuesta;
		}

		/**
		 * mostrarCoordinacionDeServiciosAsignarAcompanhante
		 *
		 * @param  int $id
		 *
		 * @return array
		 */
		public function mostrarCoordinacionDeServiciosAsignarAcompanhante($id)
		{
			$this->q =
			'SELECT
				`id`, `turnos`, `diat1`, `nombreacomt1`, `ciacompt1`, `hit1`, `hft1`, `nombreacomt2`, `ciacompt2`, `hit2`, `hft2`, `nombreacomt3`, `ciacompt3`, `hit3`, `hft3`
			FROM
				`servicios`
			WHERE
				`idinfo`	= ? AND
				`borrado`	= 0
			ORDER BY
				`diat1` DESC';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $id);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				$this->respuesta['correcto'] = true;
				if($this->r->num_rows !== 0)
				{
					$fechaActual = new DateTime();
					while($this->f = $this->r->fetch_assoc())
					{
						if(strlen($this->f['ciacompt1']) > 2 && strlen($this->f['ciacompt2']) > 2 && strlen($this->f['ciacompt3']) > 2)
							$this->respuesta['datos'][] = array
							(
								'id'				=> (int)	$this->f['id'],
								'turnos'			=> (int)	$this->f['turnos'],
								'dia'				=> (string)	$this->f['diat1'],
								'acompanhanteT1'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt1'])),
								'cedulaT1'			=> (int)	$this->f['ciacompt1'],
								'hIT1'				=> (string)	$this->f['hit1'],
								'hFT1'				=> (string)	$this->f['hft1'],
								'acompanhanteT2'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt2'])),
								'cedulaT2'			=> (int)	$this->f['ciacompt2'],
								'hIT2'				=> (string)	$this->f['hit2'],
								'hFT2'				=> (string)	$this->f['hft2'],
								'acompanhanteT3'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt3'])),
								'cedulaT3'			=> (int)	$this->f['ciacompt3'],
								'hIT3'				=> (string)	$this->f['hit3'],
								'hFT3'				=> (string)	$this->f['hft3'],
								'modificar'			=> (bool)	($fechaActual->format('Y-m-d') <= $this->f['diat1'])
							);
						else if(strlen($this->f['ciacompt1']) > 2 && strlen($this->f['ciacompt2']) > 2 && strlen($this->f['ciacompt3']) < 2)
							$this->respuesta['datos'][] = array
							(
								'id'				=> (int)	$this->f['id'],
								'turnos'			=> (int)	$this->f['turnos'],
								'dia'				=> (string)	$this->f['diat1'],
								'acompanhanteT1'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt1'])),
								'cedulaT1'			=> (int)	$this->f['ciacompt1'],
								'hIT1'				=> (string)	$this->f['hit1'],
								'hFT1'				=> (string)	$this->f['hft1'],
								'acompanhanteT2'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt2'])),
								'cedulaT2'			=> (int)	$this->f['ciacompt2'],
								'hIT2'				=> (string)	$this->f['hit2'],
								'hFT2'				=> (string)	$this->f['hft2'],
								'acompanhanteT3'	=> (string)	'- - -',
								'cedulaT3'			=> (string)	'- - -',
								'hIT3'				=> (string)	'- - -',
								'hFT3'				=> (string)	'- - -',
								'modificar'			=> (bool)	($fechaActual->format('Y-m-d') <= $this->f['diat1'])
							);
						else if(strlen($this->f['ciacompt1']) > 2 && strlen($this->f['ciacompt2']) < 2 && strlen($this->f['ciacompt3']) < 2)
							$this->respuesta['datos'][] = array
							(
								'id'				=> (int)	$this->f['id'],
								'turnos'			=> (int)	$this->f['turnos'],
								'dia'				=> (string)	$this->f['diat1'],
								'acompanhanteT1'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt1'])),
								'cedulaT1'			=> (int)	$this->f['ciacompt1'],
								'hIT1'				=> (string)	$this->f['hit1'],
								'hFT1'				=> (string)	$this->f['hft1'],
								'acompanhanteT2'	=> (string)	'- - -',
								'cedulaT2'			=> (string)	'- - -',
								'hIT2'				=> (string)	'- - -',
								'hFT2'				=> (string)	'- - -',
								'acompanhanteT3'	=> (string)	'- - -',
								'cedulaT3'			=> (string)	'- - -',
								'hIT3'				=> (string)	'- - -',
								'hFT3'				=> (string)	'- - -',
								'modificar'			=> (bool)	($fechaActual->format('Y-m-d') <= $this->f['diat1'])
							);
					}
				}
				else
					$this->respuesta['sinRegistros'] = true;
			}
			else
				$this->respuesta = array
				(
					'error'		=> (bool)	true,
					'query'		=> (bool)	true,
					'nroQuery'	=> (int)	1
				);

			return $this->respuesta;
		}

		/**
		 * insertarCoordinacionDeServiciosAsignarAcompanhante
		 *
		 * @param  int $id
		 *
		 * @return array
		 */
		public function insertarCoordinacionDeServiciosAsignarAcompanhante($id)
		{
			$this->q =
			'SELECT
				`turnos`, `diat1`, `nombreacomt1`, `ciacompt1`, `hit1`, `hft1`, `nombreacomt2`, `ciacompt2`, `hit2`, `hft2`, `nombreacomt3`, `ciacompt3`, `hit3`, `hft3`
			FROM
				`servicios`
			WHERE
				`id` = ?';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $id);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				$this->f = $this->r->fetch_assoc();
				$this->f['diat1'] = new DateTime($this->f['diat1']);
				$this->f['diat1'] = $this->f['diat1']->format('d/m/Y');
				if(strlen($this->f['ciacompt1']) > 2 && strlen($this->f['ciacompt2']) > 2 && strlen($this->f['ciacompt3']) > 2)
					$this->respuesta = array
					(
						'correcto'			=> (bool)	true,
						'id'				=> (int)	$id,
						'turnos'			=> (int)	$this->f['turnos'],
						'dia'				=> (string)	$this->f['diat1'],
						'acompanhanteT1'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt1'])),
						'cedulaT1'			=> (int)	$this->f['ciacompt1'],
						'hIT1'				=> (int)	str_replace(':', '', $this->f['hit1']),
						'hFT1'				=> (int)	str_replace(':', '', $this->f['hft1']),
						'acompanhanteT2'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt2'])),
						'cedulaT2'			=> (int)	$this->f['ciacompt2'],
						'hIT2'				=> (int)	str_replace(':', '', $this->f['hit2']),
						'hFT2'				=> (int)	str_replace(':', '', $this->f['hft2']),
						'acompanhanteT3'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt3'])),
						'cedulaT3'			=> (int)	$this->f['ciacompt3'],
						'hIT3'				=> (int)	str_replace(':', '', $this->f['hit3']),
						'hFT3'				=> (int)	str_replace(':', '', $this->f['hft3'])
					);
				else if(strlen($this->f['ciacompt1']) > 2 && strlen($this->f['ciacompt2']) > 2 && strlen($this->f['ciacompt3']) < 2)
					$this->respuesta = array
					(
						'correcto'			=> (bool)	true,
						'id'				=> (int)	$id,
						'turnos'			=> (int)	$this->f['turnos'],
						'dia'				=> (string)	$this->f['diat1'],
						'acompanhanteT1'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt1'])),
						'cedulaT1'			=> (int)	$this->f['ciacompt1'],
						'hIT1'				=> (int)	str_replace(':', '', $this->f['hit1']),
						'hFT1'				=> (int)	str_replace(':', '', $this->f['hft1']),
						'acompanhanteT2'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt2'])),
						'cedulaT2'			=> (int)	$this->f['ciacompt2'],
						'hIT2'				=> (int)	str_replace(':', '', $this->f['hit2']),
						'hFT2'				=> (int)	str_replace(':', '', $this->f['hft2'])
					);
				else if(strlen($this->f['ciacompt1']) > 2 && strlen($this->f['ciacompt2']) < 2 && strlen($this->f['ciacompt3']) < 2)
					$this->respuesta = array
					(
						'correcto'			=> (bool)	true,
						'id'				=> (int)	$id,
						'turnos'			=> (int)	$this->f['turnos'],
						'dia'				=> (string)	$this->f['diat1'],
						'acompanhanteT1'	=> (string)	ucwords(mb_strtolower($this->f['nombreacomt1'])),
						'cedulaT1'			=> (int)	$this->f['ciacompt1'],
						'hIT1'				=> (int)	str_replace(':', '', $this->f['hit1']),
						'hFT1'				=> (int)	str_replace(':', '', $this->f['hft1'])
					);
			}
			else
				$this->respuesta = array
				(
					'error'		=> (bool)	true,
					'query'		=> (bool)	true,
					'nroQuery'	=> (int)	1
				);

			return $this->respuesta;
		}

		/**
		 * informacionDeServicio
		 *
		 * @param  int $id
		 *
		 * @return array
		 */
		public function informacionDeServicio($id)
		{
			$this->q =
			'SELECT
				`id`, `id_socio`, `nombre_socio`, `obs_socio`, `fecha_ini`, `hs_x_dia`, `lugar`, `pendiente`, `activo`, `cancelado`, `finalizado`, `telefono`, `localidad`, `tipo`, `controll`
			FROM
				`pedido_acomp`
			WHERE
				`id` = ?';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $id);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				while($this->f = $this->r->fetch_assoc())
				{
					$this->f['fecha_ini'] = new DateTime($this->f['fecha_ini']);
					$this->respuesta[] = array
					(
						'id'			=> (int)	$this->f['id'],
						'cedula'		=> (string)	$this->f['id_socio'],
						'nombre'		=> (string)	ucwords(strtolower($this->f['nombre_socio'])),
						'observacion'	=> (string)	$this->f['obs_socio'],
						'fecha'			=> (string) $this->f['fecha_ini']->format('d/m/Y'),
						'horas'			=> (int)	$this->f['hs_x_dia'],
						'lugar'			=> (string) ucwords(strtolower(str_replace('_', ' ', $this->f['lugar']))),
						'pendiente'		=> (int)	$this->f['pendiente'],
						'activo'		=> (int)	$this->f['activo'],
						'cancelado'		=> (int)	$this->f['cancelado'],
						'finalizado'	=> (int)	$this->f['finalizado'],
						'telefono'		=> (string) $this->f['telefono'],
						'localidad'		=> (string) ucwords(strtolower($this->f['localidad'])),
						'tipo'			=> (string) $this->f['tipo'],
						'control'		=> (int)	$this->f['controll']
					);
				}
			}
			else
				$this->respuesta = array
				(
					'error'		=> (bool)	true,
					'query'		=> (bool)	true,
					'nroQuery'	=> (int)	1
				);

			return $this->respuesta;
		}

		/**
		 * mostrarTodosLosUsuariosConServicios
		 *
		 * @return array
		 */
		public function mostrarTodosLosUsuariosConServicios()
		{
			$this->q =
			'SELECT
				`id`, `id_socio`, `lugar`, `nombre_socio`, `obs_socio`, `fecha_ini`
			FROM
				`pedido_acomp`
			WHERE
				`pendiente`		= 1 AND
				`finalizado`	= 0 AND
				`grupo`			= ?';

			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $_SESSION['grupo']);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				while($this->f = $this->r->fetch_assoc())
				{
					$this->respuesta[] = array
					(
						'id'				=> (int)	$this->f['id'],
						'cedula'			=> (string)	$this->f['id_socio'],
						'nombre'			=> (string)	ucwords(mb_strtolower($this->f['nombre_socio'])),
						'observacion'		=> (string)	$this->f['obs_socio'],
						'lugar'				=> (string)	$this->f['lugar'],
						'fecha'				=> (string)	$this->f['fecha_ini'],
						'sinAcompanhante'	=> (bool)	true
					);
				}
			}
			else
				return $this->respuesta = array
				(
					'error'		=> (bool)	true,
					'query'		=> (bool)	true,
					'nroQuery'	=> (int)	1
				);

			$this->q =
			'SELECT
				`id`, `id_socio`, `lugar`, `nombre_socio`, `obs_socio`, `fecha_ini`
			FROM
				`pedido_acomp`
			WHERE
				`restanhrs`		= 1 AND
				`activo`		= 1 AND
				`finalizado`	= 0 AND
				`grupo`			= ?';

			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $_SESSION['grupo']);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				while($this->f = $this->r->fetch_assoc())
				{
					$this->respuesta[] = array
					(
						'id'			=> (int)	$this->f['id'],
						'cedula'		=> (string)	$this->f['id_socio'],
						'nombre'		=> (string)	ucwords(mb_strtolower($this->f['nombre_socio'])),
						'observacion'	=> (string)	$this->f['obs_socio'],
						'lugar'			=> (string)	$this->f['lugar'],
						'fecha'			=> (string)	$this->f['fecha_ini'],
						'pendiente'		=> (bool)	true
					);
				}
			}
			else
				return $this->respuesta = array
				(
					'error'		=> (bool)	true,
					'query'		=> (bool)	true,
					'nroQuery'	=> (int)	2
				);

			$this->q =
			'SELECT
				`id`, `id_socio`, `lugar`, `nombre_socio`, `obs_socio`, `fecha_ini`
			FROM
				`pedido_acomp`
			WHERE
				`restanhrs`		= 0 AND
				`activo`		= 1 AND
				`finalizado`	= 0 AND
				`grupo`			= ?';

			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $_SESSION['grupo']);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				while($this->f = $this->r->fetch_assoc())
				{
					$this->respuesta[] = array
					(
						'id'			=> (int)	$this->f['id'],
						'cedula'		=> (string)	$this->f['id_socio'],
						'nombre'		=> (string)	ucwords(mb_strtolower($this->f['nombre_socio'])),
						'observacion'	=> (string)	$this->f['obs_socio'],
						'lugar'			=> (string)	$this->f['lugar'],
						'fecha'			=> (string)	$this->f['fecha_ini'],
						'completo'		=> (bool)	true
					);
				}
			}
			else
				return $this->respuesta = array
				(
					'error'		=> (bool)	true,
					'query'		=> (bool)	true,
					'nroQuery'	=> (int)	3
				);

			return $this->respuesta;
		}

		/**
		 * posiblesReintegros
		 *
		 * @param  int $cedula
		 *
		 * @return array
		 */
		public function posiblesReintegros($cedula)
		{
			$this->mysqli = parent::padron();
			$this->q =
			'SELECT
				`fecha_afiliacion`
			FROM
				`padron_producto_socio`
			WHERE
				`cedula`	= ? AND
				(
					`servicio`	= "06" OR
					`servicio`	= "07"
				)';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('s', $cedula);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				if($this->r->num_rows !== 0)
				{
					$this->f = $this->r->fetch_assoc();
					$this->mysqli = parent::localhost();
					$this->q =
					'SELECT
						`s`.`id`, `s`.`idinfo`, MIN(`s`.`diat1`) AS `desde`, MAX(`s`.`diat1`) AS `hasta`, `s`.`turnos`
					FROM
						`servicios` AS `s`
					INNER JOIN
						`pedido_acomp` AS `p`
						ON `s`.`idinfo` = `p`.`id`
					WHERE
						`p`.`id_socio` = ?
					GROUP BY
						`s`.`idinfo`
					HAVING
						MIN(`s`.`diat1`) > ?
					ORDER BY
						`s`.`diat1` DESC';
					$this->r = $this->mysqli->prepare($this->q);
					$this->r->bind_param('ss', $cedula, $this->f['fecha_afiliacion']);
					if($this->r->execute())
					{
						$this->r = $this->r->get_result();
						$this->respuesta['correcto'] = true;
						if($this->r->num_rows !== 0)
						{
							while($this->f = $this->r->fetch_assoc())
							{
								$this->f['desde'] = new DateTime($this->f['desde']);
								$this->f['hasta'] = new DateTime($this->f['hasta']);
								$this->respuesta['datos'][] = array
								(
									'id'		=> (int)	$this->f['idinfo'],
									'desde'		=> (string)	$this->f['desde']->format('d/m/Y'),
									'hasta'		=> (string)	$this->f['hasta']->format('d/m/Y'),
									'cantidad'	=> (int)	$this->f['hasta']->diff($this->f['desde'])->d,
									'turnos'	=> (int)	$this->f['turnos']
								);
							}
						}
						else
							$this->respuesta['sinRegistros'] = true;
					}
					else
						$this->respuesta = array
						(
							'error'		=> (bool)	true,
							'query'		=> (bool)	true,
							'norQuery'	=> (int)	1
						);
				}
				else
					$this->respuesta = array
					(
						'error'			=> (bool)	true,
						'sinReintegro'	=> (bool)	true
					);
			}
			else
				$this->respuesta = array
				(
					'error'		=> (bool)	true,
					'query'		=> (bool)	true,
					'norQuery'	=> (int)	1
				);

			return $this->respuesta;
		}

		/**
		 * formularioReintegro
		 *
		 * @param  int $id
		 *
		 * @return array
		 */
		public function formularioReintegro($id)
		{
			$this->q =
			'SELECT
				*
			FROM
				`pedido_acomp` AS `p`
			INNER JOIN
				`servicios` AS `s`
				ON `p`.`id` = `s`.`idinfo`
			WHERE
				`p`.`id` = ?';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $id);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				$this->respuesta['correcto'] = true;
				if($this->r->num_rows !== 0)
				{

				}
				else
					$this->respuesta = array
					(
						'error'			=> (bool)	true,
						'sinReintegro'	=> (bool)	true
					);
			}
			else
				$this->respuesta = array
				(
					'error'		=> (bool)	true,
					'query'		=> (bool)	true,
					'norQuery'	=> (int)	1
				);

			return $this->respuesta;
		}

		/**
		 * tomarPedido
		 *
		 * @param  string	$nombre
		 * @param  int		$cedula
		 * @param  int		$telefono
		 * @param  int		$lugarInternacion
		 * @param  string	$departamento
		 * @param  string	$servicio
		 * @param  string	$observaciones
		 * @param  string	$sala
		 * @param  string	$cama
		 * @param  int		$piso
		 * @param  int		$horasDeServicio
		 * @param  string	$fechaInicio
		 * @param  int		$horaInicio
		 *
		 * @return array
		 */
		public function tomarPedido($nombre, $cedula, $telefono, $lugarInternacion, $departamento, $servicio, $observaciones, $sala, $cama, $piso, $horasDeServicio, $fechaInicio, $horaInicio)
		{
			$this->q =
			'INSERT INTO
				`coni`
			(numcontrol)
				VALUES
			(?)';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('s', (date('Y-m-d H:i:s')));
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				// bug 1 el insert_id sobre la query no sobre la conexion
				$nroControl = $this->mysqli->insert_id;

				$this->q =
				'SELECT
					`departamento`, `nombre`
				FROM
					`lugar`
				WHERE
					`id` =  ?';
				$this->r = $this->mysqli->prepare($this->q);
				$this->r->bind_param('i', $lugarInternacion);
				if($this->r->execute())
				{
					$this->r = $this->r->get_result();
					$this->f = $this->r->fetch_assoc();

					
					// escrito por Diego
					$contar = $this->r->num_rows;

					if($contar > 0){
						$lugar = $this->f['nombre'];
						$localidad = $this->f['departamento'];
					}else{
						$lugar = $lugarInternacion;
						if($departamento != '0'){
							$localidad = $departamento;
						}else{
							$respuesta = array
							(
								'error'		=> true,
								'query'		=> false,
								'departamentoVacio'=>true
							);
							return $respuesta;
							die();
						}
					}

					$nombre = ucwords(mb_strtolower($nombre));
					$fecha_carga = date('Y-m-d H:i:s');
					$usuario1 = $_SESSION['usuario'];
					$pendiente = 1;
					$zero = 0;
					$area = $_SESSION['area'];
					$grupo = $_SESSION['grupo'];
					$nivel = $_SESSION['nivel'];
					$hora_carga = time();
					$tiempoPedido = date('Y-m-d H:i:s');

					$this->parametros = array
					(
						'id_socio'		=> (int)	$cedula,
						'nombre_socio'	=> (string)	ucwords(mb_strtolower($nombre)),
						'obs_socio'		=> (string)	$observaciones,
						'fecha_ini'		=> (string)	$fechaInicio,
						'hs_ini'		=> (int)	$horaInicio,
						'hs_x_dia'		=> (int)	$horasDeServicio,
						'lugar'			=> (string)	$this->f['nombre'],
						'piso'			=> (int)	$piso,
						'sala'			=> (string)	$sala,
						'cama'			=> (string)	$cama,
						'fecha_carga'	=> (string)	date('Y-m-d H:i:s'),
						'usuario1'		=> (string)	$_SESSION['usuario'],
						'pendiente'		=> (int)	1,
						'activo'		=> (int)	0,
						'cancelado'		=> (int)	0,
						'finalizado'	=> (int)	0,
						'acompanante'	=> (int)	0,
						'conacomp'		=> (int)	0,
						'area'			=> (int)	$_SESSION['area'],
						'grupo'			=> (int)	$_SESSION['grupo'],
						'nivel'			=> (int)	$_SESSION['nivel'],
						'telefono'		=> (int)	$telefono,
						'hora_carga'	=> (string)	time(),
						'localidad'		=> (string)	$this->f['departamento'],
						'tiempopedido'	=> (string)	date('Y-m-d H:i:s'),
						'tipo'			=> (string)	$servicio,
						'controll'		=> (int)	$nroControl,
						'activocon'		=> (int)	0,
						'dejopend'		=> (int)	0
					);

					/*$this->q =
					"INSERT INTO
						`pedido_acomp_copy1`
					(id_socio, nombre_socio, obs_socio, fecha_ini, hs_ini, hs_x_dia, lugar, piso, sala, cama, fecha_carga, usuario1, pendiente, activo, cancelado, finalizado, acompanante, conacomp, area, grupo, nivel, telefono, hora_carga, localidad, tiempopedido, tipo, controll, activocon, dejopend)
						VALUES
					('$cedula', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";*/

					// bug 2 la parametrizacion no funcionaba

					$this->q =
					"INSERT INTO
						`pedido_acomp`
					(id_socio, nombre_socio, obs_socio, fecha_ini, hs_ini, hs_x_dia, lugar, piso, sala, cama, fecha_carga, usuario1, pendiente, activo, cancelado, finalizado, acompanante, conacomp, area, grupo, nivel, telefono, hora_carga, localidad, tiempopedido, tipo, controll, activocon, dejopend)
						VALUES
						( '$cedula', '$nombre', '$observaciones', '$fechaInicio', '$horaInicio', '$horasDeServicio', '$lugar',
					 '$piso', '$sala', '$cama', '$fecha_carga', '$usuario1', '$pendiente', '$zero', '$zero', '$zero', '$zero', '$zero', '$area', '$grupo', '$nivel', '$telefono', '$hora_carga', '$localidad',
					  '$tiempoPedido', '$servicio', '$nroControl', '$zero', '$zero')";

					$this->r = $this->mysqli->prepare($this->q);
					/*$this->r->bind_param('iiiiiiiiiiiiiiiiiiiiiiiiiiiii' $cedula, $nombre, $observaciones, $fechaInicio, $horaInicio, $horasDeServicio, $lugar,
					 $piso, $sala, $cama, $fecha_carga, $usuario1, $pendiente, $zero, $zero, $zero, $zero, $zero, $area, $grupo, $nivel, $telefono, $hora_carga, $localidad,
					  $tiempoPedido, $servicio, $nroControl, $zero, $zero);*/

					if($this->r->execute())
					{
						$this->respuesta = array
						(
							'correcto'		=> true,
							'nroControl'	=> $this->parametros['controll']
						);
					}
					else
						$this->respuesta = array
						(
							'error'		=> true,
							'query'		=> true,
							'nroQuery'	=> 3
						);
				}
				else
					$this->respuesta = array
					(
						'error'		=> true,
						'query'		=> true,
						'nroQuery'	=> 2
					);
			}
			else
				$this->respuesta = array
				(
					'error'		=> true,
					'query'		=> true,
					'nroQuery'	=> 1
				);

			return $this->respuesta;
		}

		/**
		 * guardarCoordinacionDeServiciosAsignarAcompanhante
		 *
		 * @param  int		$id
		 * @param  int		$referencia
		 * @param  string	$fecha
		 * @param  int		$cantidadTurnos
		 * @param  string	$nombreAcompanhanteT1
		 * @param  string	$cedulaAcompanhanteT1
		 * @param  string	$desdeT1
		 * @param  string	$hastaT1
		 * @param  string	$nombreAcompanhanteT2
		 * @param  string	$cedulaAcompanhanteT2
		 * @param  string	$desdeT2
		 * @param  string	$hastaT2
		 * @param  string	$nombreAcompanhanteT3
		 * @param  string	$cedulaAcompanhanteT3
		 * @param  string	$desdeT3
		 * @param  string	$hastaT3
		 *
		 * @return array
		 */
		public function guardarCoordinacionDeServiciosAsignarAcompanhante($id, $referencia, $fecha, $cantidadTurnos, $nombreAcompanhanteT1, $cedulaAcompanhanteT1, $desdeT1, $hastaT1, $nombreAcompanhanteT2, $cedulaAcompanhanteT2, $desdeT2, $hastaT2, $nombreAcompanhanteT3, $cedulaAcompanhanteT3, $desdeT3, $hastaT3)
		{
			switch ($cantidadTurnos)
			{
				case 1:
					$cantidadHorasT1		= (explode(':', $hastaT1)[0] > explode(':', $desdeT1)[0])
						? (int)((int)explode(':', $hastaT1)[0] - (int)explode(':', $desdeT1)[0])
						: (int)(int)((int)(explode(':', $hastaT1)[0] + 24) - (int)explode(':', $desdeT1)[0]);

					$cantidadHorasT2		= (int)0;
					$cantidadHorasT3		= (int)0;

					$nombreAcompanhanteT2	= null;
					$cedulaAcompanhanteT2	= null;
					$desdeT2				= null;
					$hastaT2				= null;

					$nombreAcompanhanteT3	= null;
					$cedulaAcompanhanteT3	= null;
					$desdeT3				= null;
					$hastaT3				= null;
					break;
				case 2:
					$cantidadHorasT1		= (explode(':', $hastaT1)[0] > explode(':', $desdeT1)[0])
						? (int)((int)explode(':', $hastaT1)[0] - (int)explode(':', $desdeT1)[0])
						: (int)(int)((int)(explode(':', $hastaT1)[0] + 24) - (int)explode(':', $desdeT1)[0]);

					$cantidadHorasT2		= (explode(':', $hastaT2)[0] > explode(':', $desdeT2)[0])
						? (int)((int)explode(':', $hastaT2)[0] - (int)explode(':', $desdeT2)[0])
						: (int)(int)((int)(explode(':', $hastaT2)[0] + 24) - (int)explode(':', $desdeT2)[0]);

					$cantidadHorasT3		= (int)0;

					$nombreAcompanhanteT3	= null;
					$cedulaAcompanhanteT3	= null;
					$desdeT3				= null;
					$hastaT3				= null;
					break;
				case 3:
					$cantidadHorasT1		= (explode(':', $hastaT1)[0] > explode(':', $desdeT1)[0])
						? (int)((int)explode(':', $hastaT1)[0] - (int)explode(':', $desdeT1)[0])
						: (int)(int)((int)(explode(':', $hastaT1)[0] + 24) - (int)explode(':', $desdeT1)[0]);

					$cantidadHorasT2		= (explode(':', $hastaT2)[0] > explode(':', $desdeT2)[0])
						? (int)((int)explode(':', $hastaT2)[0] - (int)explode(':', $desdeT2)[0])
						: (int)(int)((int)(explode(':', $hastaT2)[0] + 24) - (int)explode(':', $desdeT2)[0]);

					$cantidadHorasT3		= (explode(':', $hastaT3)[0] > explode(':', $desdeT3)[0])
						? (int)((int)explode(':', $hastaT3)[0] - (int)explode(':', $desdeT3)[0])
						: (int)(int)((int)(explode(':', $hastaT3)[0] + 24) - (int)explode(':', $desdeT3)[0]);
					break;
			}

			$cantidadTotal = array
			(
				'total' =>($cantidadHorasT1 + $cantidadHorasT2 + $cantidadHorasT3)
			);

			return $cantidadTotal;
/*
			$this->q =
			'SELECT
				`lugar`
			FROM
				`pedido_acomp`
			WHERE
				`id` = ?';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $referencia);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				$this->f = $this->r->fetch_assoc();
				$this->parametros = array
				(
					'diat1'			=> (string)	$fecha->format('Y-m-d'),
					'nombreacomt1'	=> (string)	ucwords(mb_strtolower($nombreAcompanhanteT1)),
					'ciacompt1'		=> (string)	$cedulaAcompanhanteT1,
					'hit1'			=> (string)	$desdeT1,
					'hft1'			=> (string)	$hastaT1,
					'lugart1'		=> (string)	$this->f['lugar'],
					'nombreacomt2'	=> (string)	ucwords(mb_strtolower($nombreAcompanhanteT2)),
					'ciacompt2'		=> (string)	$cedulaAcompanhanteT2,
					'hit2'			=> (string)	$desdeT2,
					'hft2'			=> (string)	$hastaT2,
					'nombreacomt3'	=> (string)	ucwords(mb_strtolower($nombreAcompanhanteT3)),
					'ciacompt3'		=> (string)	$cedulaAcompanhanteT3,
					'hit3'			=> (string)	$desdeT3,
					'hft3'			=> (string)	$hastaT3,
					'idinfo'		=> (int)	$referencia,
					'fechabd'		=> (string)	date('Y-m-d H:i:s'),
					'nivel'			=> (string)	$_SESSION['nivel'],
					'area'			=> (string)	$_SESSION['area'],
					'grupo'			=> (string)	$_SESSION['grupo'],
					'ht1'			=> (int)	$cantidadHorasT1,
					'ht2'			=> (int)	$cantidadHorasT2,
					'ht3'			=> (int)	$cantidadHorasT3,
					'hcompleta'		=> (int)	$cantidadTotal,
					'turnos'		=> (int)	$cantidadTurnos,
					'borrado'		=> (int)	0,
					'activo'		=> (int)	0,
					'horaint1'		=> (string)	'',
					'horafint1'		=> (string)	'',
					'horaint2'		=> (string)	'',
					'horafint2'		=> (string)	'',
					'horaint3'		=> (string)	'',
					'horafint3'		=> (string)	'',
					'chk'			=> (int)	0
				);


				if($id !== '')
				{
					$this->q =
					'UPDATE
						`servicios`
					SET
						`diat1`			= ?,
						`nombreacomt1`	= ?,
						`ciacompt1`		= ?,
						`hit1`			= ?,
						`hft1`			= ?,
						`lugart1`		= ?,
						`nombreacomt2`	= ?,
						`ciacompt2`		= ?,
						`hit2`			= ?,
						`hft2`			= ?,
						`nombreacomt3`	= ?,
						`ciacompt3`		= ?,
						`hit3`			= ?,
						`hft3`			= ?,
						`idinfo`		= ?,
						`fechabd`		= ?,
						`nivel`			= ?,
						`area`			= ?,
						`grupo`			= ?,
						`ht1`			= ?,
						`ht2`			= ?,
						`ht3`			= ?,
						`hcompleta`		= ?,
						`turnos`		= ?,
						`borrado`		= ?,
						`activo`		= ?,
						`horaint1`		= ?,
						`horafint1`		= ?,
						`horaint2`		= ?,
						`horafint2`		= ?,
						`horaint3`		= ?,
						`horafint3`		= ?,
						`chk`			= ?
						WHERE
							`id`		= ?';
					$this->r = $this->mysqli->prepare($this->q);
					$this->r->bind_param('ssisssssssssssisiiiiiiiiiissssssii', $this->parametros['diat1'], $this->parametros['nombreacomt1'], $this->parametros['ciacompt1'], $this->parametros['hit1'], $this->parametros['hft1'], $this->parametros['lugart1'], $this->parametros['nombreacomt2'], $this->parametros['ciacompt2'], $this->parametros['hit2'], $this->parametros['hft2'], $this->parametros['nombreacomt3'], $this->parametros['ciacompt3'], $this->parametros['hit3'], $this->parametros['hft3'], $this->parametros['idinfo'], $this->parametros['fechabd'], $this->parametros['nivel'], $this->parametros['area'], $this->parametros['grupo'], $this->parametros['ht1'], $this->parametros['ht2'], $this->parametros['ht3'], $this->parametros['hcompleta'], $this->parametros['turnos'], $this->parametros['borrado'], $this->parametros['activo'], $this->parametros['horaint1'], $this->parametros['horafint1'], $this->parametros['horaint2'], $this->parametros['horafint2'], $this->parametros['horaint3'], $this->parametros['horafint3'], $this->parametros['chk'], $id);
				}
				else
				{
					$this->q =
					'INSERT INTO
						`servicios`
						(`diat1`, `nombreacomt1`, `ciacompt1`, `hit1`, `hft1`, `lugart1`, `nombreacomt2`, `ciacompt2`, `hit2`, `hft2`, `nombreacomt3`, `ciacompt3`, `hit3`, `hft3`, `idinfo`, `fechabd`, `nivel`, `area`, `grupo`, `ht1`, `ht2`, `ht3`, `hcompleta`, `turnos`, `borrado`, `activo`, `horaint1`, `horafint1`, `horaint2`, `horafint2`, `horaint3`, `horafint3`, `chk`)
					VALUES
						(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
					$this->r = $this->mysqli->prepare($this->q);
					$this->r->bind_param('ssisssssssssssisiiiiiiiiiissssssi', $this->parametros['diat1'], $this->parametros['nombreacomt1'], $this->parametros['ciacompt1'], $this->parametros['hit1'], $this->parametros['hft1'], $this->parametros['lugart1'], $this->parametros['nombreacomt2'], $this->parametros['ciacompt2'], $this->parametros['hit2'], $this->parametros['hft2'], $this->parametros['nombreacomt3'], $this->parametros['ciacompt3'], $this->parametros['hit3'], $this->parametros['hft3'], $this->parametros['idinfo'], $this->parametros['fechabd'], $this->parametros['nivel'], $this->parametros['area'], $this->parametros['grupo'], $this->parametros['ht1'], $this->parametros['ht2'], $this->parametros['ht3'], $this->parametros['hcompleta'], $this->parametros['turnos'], $this->parametros['borrado'], $this->parametros['activo'], $this->parametros['horaint1'], $this->parametros['horafint1'], $this->parametros['horaint2'], $this->parametros['horafint2'], $this->parametros['horaint3'], $this->parametros['horafint3'], $this->parametros['chk']);
				}

				if($this->r->execute())
				{
					$this->q =
					'UPDATE
						`pedido_acomp`
					SET
						activo		= 1,
						pendiente	= 0
					WHERE
						id = ?';
					$this->r = $this->mysqli->prepare($this->q);
					$this->r->bind_param('i', $this->parametros['idinfo']);
					if($this->r->execute())
					{
						$this->q =
						'SELECT
							`hcompleta`
						FROM
							`servicios`
						WHERE
							`hcompleta`	= 1 AND
							`idinfo`	= ?';
						$this->r = $this->mysqli->prepare($this->q);
						$this->r->bind_param('i', $this->parametros['idinfo']);
						if($this->r->execute())
						{
							$this->r = $this->r->get_result();
							while($this->f = $this->r->fetch_assoc())
							{
								$this->q = ((int) $this->f['hcompleta'] === 1)
									?
									'UPDATE
										`pedido_acomp`
									SET
										`restanhrs`	= 1
									WHERE
										`id` = ?'
									:
									'UPDATE
										`pedido_acomp`
									SET
										`restanhrs`	= 0
									WHERE
										`id` = ?';
								$this->r2 = $this->mysqli->prepare($this->q);
								$this->r2->bind_param('i', $this->parametros['idinfo']);
								if($this->r2->execute())
									$this->respuesta = array
									(
										'correcto' => true
									);
								else
									$this->respuesta = array
									(
										'error'		=> true,
										'query'		=> true,
										'nroQuery'	=> 5
									);
							}
						}
						else
							$this->respuesta = array
							(
								'error'		=> true,
								'query'		=> true,
								'nroQuery'	=> 4
							);
					}
					else
						$this->respuesta = array
						(
							'error'		=> true,
							'query'		=> true,
							'nroQuery'	=> 3
						);
				}
				else
					$this->respuesta = array
					(
						'error'		=> true,
						'query'		=> true,
						'nroQuery'	=> 2
					);
			}
			else
				$this->respuesta = array
				(
					'error'		=> true,
					'query'		=> true,
					'nroQuery'	=> 1
				);

			return $this->respuesta;
			*/

		}

		/**
		 * coordinacionDeServiciosModificarDatosFinal
		 *
		 * @param  int		$id
		 * @param  string	$telefono
		 * @param  int		$lugarInternacion
		 * @param  string	$servicio
		 * @param  string	$observaciones
		 * @param  string	$sala
		 * @param  string	$cama
		 * @param  int		$piso
		 * @param  int		$horasDeServicio
		 * @param  string	$fechaInicio
		 * @param  int		$horaInicio
		 *
		 * @return array
		 */
		public function coordinacionDeServiciosModificarDatosFinal($id, $telefono, $lugarInternacion, $servicio, $observaciones, $sala, $cama, $piso, $horasDeServicio, $fechaInicio, $horaInicio)
		{
			$this->q =
			'SELECT
				`departamento`
			FROM
				`lugar`
			WHERE
				`id` =  ?';
			$this->r = $this->mysqli->prepare($this->q);
			$this->r->bind_param('i', $lugarInternacion);
			if($this->r->execute())
			{
				$this->r = $this->r->get_result();
				$this->r = $this->r->fetch_assoc();
				$this->parametros = array
				(
					'telefono'	=> (string)	$telefono,
					'lugar'		=> (string)	$lugarInternacion,
					'tipo'		=> (string)	$servicio,
					'obs_socio'	=> (string)	$observaciones,
					'sala'		=> (string)	$sala,
					'cama'		=> (string)	$cama,
					'piso'		=> (int)	$piso,
					'hs_ini'	=> (int)	$horasDeServicio,
					'fecha_ini'	=> (string)	$fechaInicio,
					'hs_x_dia'	=> (int)	$horaInicio,
					'localidad'	=> (string)	$this->r['departamento'],
					'id'		=> (int)	$id
				);

				$this->q =
				'UPDATE
					`pedido_acomp`
				SET
					`telefono`		= ?,
					`lugar`			= ?,
					`tipo`			= ?,
					`obs_socio`		= ?,
					`sala`			= ?,
					`cama`			= ?,
					`piso`			= ?,
					`hs_ini`		= ?,
					`fecha_ini`		= ?,
					`hs_x_dia`		= ?,
					`localidad`		= ?
				WHERE
					`id` = ?';

				$this->r = $this->mysqli->prepare($this->q);
				$this->r->bind_param('ssssssiisisi', $this->parametros['telefono'], $this->parametros['lugar'], $this->parametros['tipo'], $this->parametros['obs_socio'], $this->parametros['sala'], $this->parametros['cama'], $this->parametros['piso'], $this->parametros['hs_ini'], ($this->parametros['fecha_ini']->format('Y-m-d')), $this->parametros['hs_x_dia'], $this->parametros['localidad'], $this->parametros['id']);
				if($this->r->execute())
				{
					$this->respuesta = array
					(
						'correcto' => true
					);
				}
				else
					$this->respuesta = array
					(
						'error'		=> true,
						'query'		=> true,
						'nroQuery'	=> 2
					);
			}
			else
				$this->respuesta = array
				(
					'error'		=> true,
					'query'		=> true,
					'nroQuery'	=> 1
				);

			return $this->respuesta;
		}
	}