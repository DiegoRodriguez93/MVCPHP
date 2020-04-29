'use strict';

$(function()
{
	$('.solo_numeros').attr('pattern', '\\d*');
	$('.solo_numeros').attr('type', 'number');
	$('.solo_numeros').on('keydown,paste,keyup',function(e)
	{
		if(/[\d]/.test(e) || e.keyCode === 8 || e.keyCode === 13)
			return;
		else if((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105))
			e.preventDefault();
		else if(e.altKey)
			return false;
	});

	$('.datepicker').datepicker(configuracionDatePicker);

	window.localStorage.removeItem('detallesDeServicio');
	Auxiliares.cambiarTituloActual();

	$('#hamburguesa').click(e =>
	{
		e.preventDefault();
		Visual.animacionMenu();
	});

	//LOGIN

	$('#botonLogIn').click(e =>
	{
		e.preventDefault();
		AJAX.logIn();
	});

	$('#logInDiv').keypress(e =>
	{
		if(e.which === 13)
			AJAX.logIn();
	});

	//NAVBAR

	$('#tomarPedidoBoton').click(e=>
	{
		e.preventDefault();
		Auxiliares.cambiarTituloActual('Toma pedido');
		Visual.mostrarDivPrincipal('tomarPedidoDiv');
		if($('#menu').hasClass('menuabierto'))
			Visual.animacionMenu();
	});

	$('#coordinacionDeServiciosBoton').click(e=>
	{
		e.preventDefault();
		Auxiliares.cambiarTituloActual('Coordinación de servicios');
		AJAX.mostrarDatosBarraCoordinacion();
		Visual.mostrarDivPrincipal('coordinacionDeServiciosDiv');
		$('#coordinacionDeServiciosDivAuxiliar').show();
		$('#coordinacionDeServiciosBotones').show();
		if($('#menu').hasClass('menuabierto'))
			Visual.animacionMenu();
	});

	$('#reportesBoton').click(e=>
	{
		e.preventDefault();
		Auxiliares.cambiarTituloActual('Reportes');
		Visual.mostrarDivPrincipal('reportesDiv');
		if($('#menu').hasClass('menuabierto'))
			Visual.animacionMenu();
	});

	$('#cerrarSesionBoton').click(e =>
	{
		e.preventDefault();
		window.location.href = 'cerrarSesion.php';
	});

	//TOMAR PEDIDO

	Auxiliares.llenarHorarios(6, 24, 'tomarPedidoHoraInicio');
	AJAX.llenarLugaresDeInternacion('tomarPedidoLugarInternacion');
	Auxiliares.llenarHorarios(6, 24, 'tomarPedidoHoraInicioNoSocio');
	AJAX.llenarLugaresDeInternacion('tomarPedidoLugarInternacionNoSocio');

	$('#tomarPedidoCancelar').click(e =>
	{
		e.preventDefault();
		if(confirm('¿Desea cancelar el pedido?'))
			location.reload(true);
	});

	$('#tomarPedidoBuscarCedulaBoton').click(e =>
	{
		e.preventDefault();
		AJAX.buscarCedula('tomarPedido');
	});

	$('#tomarPedidoBuscarNYABoton').click(e =>
		{
			e.preventDefault();
			let NYA = $('#tomarPedidoBuscarNYA').val() 

			if(NYA.length <= 3){
				alert('Ingrese un nombre y/o apellido valido');
				return false;
			}

			AJAX.buscarNYA();

		});

	$('#tomarPedidoDiv').keypress(e =>
	{
		if(e.which === 13)
			AJAX.buscarCedula('tomarPedido');
	});

	$('#tomarPedidoEnviar').click(e =>
	{
		e.preventDefault();
		AJAX.tomarPedido();
	});

	$('#tomarPedidoEnviarNoSocio').click(e =>
	{
		e.preventDefault();
		AJAX.tomarPedidoNoSocio();
	});

	$('#tomarPedidoTipoDeServicio').change(() =>
	{

		if($('#tomarPedidoTipoDeServicio option:selected').text() == 'Convalecencia'){
			let html = `<span>Lugar de internación:</span>
			<input type="text" id="tomarPedidoLugarInternacion" " />
			<small id="tomarPedidoLugarInternacionError" style="display: block"></small>`;

			$('#tomarPedidoLugarInternacion').parent().html(html);

			AJAX.llenarDepartamentos('tomarPedidoDepartamento');
			$('#tomarPedidoDepartamentoSpan').css('display','block');
			$('#tomarPedidoDepartamento').css('display','block');
			
			console.log('vamos bien')
		}else{

			let html = `<span>Lugar de internación:</span>
			<select id="tomarPedidoLugarInternacion" style="border-color:rgb(206, 212, 218);></select>
			<small id="tomarPedidoLugarInternacionError" style="display: block"></small>`;

			$('#tomarPedidoLugarInternacion').parent().html(html);

			AJAX.llenarLugaresDeInternacion('tomarPedidoLugarInternacion');
			$('#tomarPedidoDepartamentoSpan').css('display','none');
			$('#tomarPedidoDepartamento').css('display','none');
			
		}

		if(Control.posiblesReintegros())
			AJAX.posiblesReintegros($('#tomarPedidoCedula').val());
		else
		{

		}
	});

	//COORDINACIÓN DE SERVICIOS

	$('.modificardatos').click(e =>
	{
		e.preventDefault();
		AJAX.coordinacionDeServiciosModificarDatos();
	});

	$('.pdayer').click(e =>
	{
		e.preventDefault();
		tituloDeSector = 'Coordinación de servicios - Turnos pendientes de ayer';
		AJAX.coordinacionDeServiciosPendientes('-1');
	});

	$('.pdahoy').click(e =>
	{
		e.preventDefault();
		tituloDeSector = 'Coordinación de servicios - Turnos pendientes de hoy';
		AJAX.coordinacionDeServiciosPendientes('0');
	});

	$('.pdmanana').click(e =>
	{
		e.preventDefault();
		tituloDeSector = 'Coordinación de servicios - Turnos pendientes de mañana';
		AJAX.coordinacionDeServiciosPendientes('+1');
	});

	$('.sinacompananteasignado').click(e =>
	{
		e.preventDefault();
		tituloDeSector = 'Coordinación de servicios - Servicios sin acompañante asignado';
		AJAX.coordinacionDeServiciosActivos('sinAcompanhante');
	});

	$('.serviciosactivospendientes').click(e =>
	{
		e.preventDefault();
		tituloDeSector = 'Coordinación de servicios - Servicios activos pendientes';
		AJAX.coordinacionDeServiciosActivos('pendiente');
	});

	$('.serviciosactivoscompletos').click(e =>
	{
		e.preventDefault();
		tituloDeSector = 'Coordinación de servicios - Servicios activos completos';
		AJAX.coordinacionDeServiciosActivos('completo');
	});

	$('#buscarAcompanhanteNavBar').keyup(e =>
	{
		if(e.which === 13)
			AJAX.traerInfoAcompanhante();
	});

	$('#buscarAcompanhanteNavBarBoton').click(e =>
	{
		AJAX.traerInfoAcompanhante();
	});

	$('#buscarAcompanhanteNavBarClon').keyup(e =>
	{
		$('#buscarAcompanhanteNavBar').val($('#buscarAcompanhanteNavBarClon').val());
		if(e.which === 13)
			AJAX.traerInfoAcompanhante();
	});

	$('#buscarAcompanhanteNavBarBotonClon').click(e =>
	{
		$('#buscarAcompanhanteNavBar').val($('#buscarAcompanhanteNavBarClon').val());
		AJAX.traerInfoAcompanhante();
	});

	$('#buscarClienteNavBar').keyup(e =>
	{
		if(e.which === 13)
			AJAX.traerInfoCliente();
	});

	$('#buscarClienteNavBarBoton').click(e =>
	{
		AJAX.traerInfoCliente();
	});

	$('#buscarClienteNavBarClon').keyup(e =>
	{
		$('#buscarClienteNavBar').val($('#buscarClienteNavBarClon').val());
		if(e.which === 13)
			AJAX.traerInfoCliente();
	});

	$('#buscarClienteNavBarBotonClon').click(e =>
	{
		$('#buscarClienteNavBar').val($('#buscarClienteNavBarClon').val());
		AJAX.traerInfoCliente();
	});

	$('#coordinacionDeServiciosModificarDatosFinalCancelar').click(e =>
	{
		if(confirm('¿Desea salir sin modificar?'))
			location.reload(true);
	});

	$('#coordinacionDeServiciosAsignarAcompanhanteNuevoRegistro').click(() =>
	{
		$('#coordinacionDeServiciosAsignarAcompanhanteTitulo').text('Coordinar un nuevo servicio.');
		$('#coordinacionDeServiciosAsignarAcompanhanteID').val('');
		Visual.limpiar(
		[
			'coordinacionDeServiciosAsignarAcompanhanteFecha',
			'coordinacionDeServiciosAsignarAcompanhanteNombreTurno1', 'coordinacionDeServiciosAsignarAcompanhanteCedulaTurno1',
			'coordinacionDeServiciosAsignarAcompanhanteNombreTurno2', 'coordinacionDeServiciosAsignarAcompanhanteCedulaTurno2',
			'coordinacionDeServiciosAsignarAcompanhanteNombreTurno3', 'coordinacionDeServiciosAsignarAcompanhanteCedulaTurno3'
		],
		true);

		Visual.limpiar(
		[
			'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno1',
			'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno2',
			'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno3', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno3'
		]);

		$('#coordinacionDeServiciosAsignarAcompanhanteTurnos').val(0);
		$('#coordinacionDeServiciosAsignarAcompanhanteTurnos').change();

		window.scrollTo(0, 0);

		$('#coordinacionDeServiciosAsignarAcompanhanteDiv').show();
	});

	$('#coordinacionDeServiciosAsignarAcompanhanteTurnos').change(() =>
	{
		switch (parseInt($('#coordinacionDeServiciosAsignarAcompanhanteTurnos').val()))
		{
			case 1:
				$('#turno1div').show();
				$('#turno2div').hide();
				$('#turno3div').hide();
				break;

			case 2:
				$('#turno1div').show();
				$('#turno2div').show();
				$('#turno3div').hide();
				break;

			case 3:
				$('#turno1div').show();
				$('#turno2div').show();
				$('#turno3div').show();
				break;

			default:
				Visual.limpiar(
				[
					'coordinacionDeServiciosAsignarAcompanhanteFecha',
					'coordinacionDeServiciosAsignarAcompanhanteNombreTurno1', 'coordinacionDeServiciosAsignarAcompanhanteCedulaTurno1',
					'coordinacionDeServiciosAsignarAcompanhanteNombreTurno2', 'coordinacionDeServiciosAsignarAcompanhanteCedulaTurno2',
					'coordinacionDeServiciosAsignarAcompanhanteNombreTurno3', 'coordinacionDeServiciosAsignarAcompanhanteCedulaTurno3'
				],
				true);

				Visual.limpiar(
				[
					'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno1',
					'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno2',
					'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno3', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno3'
				]);

				$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1 :first-child').prop('selected', true);
				$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno1 :first-child').prop('selected', true);
				$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2 :first-child').prop('selected', true);
				$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno2 :first-child').prop('selected', true);
				$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno3 :first-child').prop('selected', true);
				$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno3 :first-child').prop('selected', true);

				$('#turno1div').hide();
				$('#turno2div').hide();
				$('#turno3div').hide();
				break;
		}

		Visual.limpiar('coordinacionDeServiciosAsignarAcompanhanteTurnos');
	});

	$('#coordinacionDeServiciosAsignarAcompanhanteEnviar').click(e =>
	{
		e.preventDefault();
		if(Control.guardarCoordinacionDeServiciosAsignarAcompanhante())
			AJAX.guardarCoordinacionDeServiciosAsignarAcompanhante();
	});

	$('.todosLosServiciosActuales').click(e =>
	{
		e.preventDefault();
		AJAX.mostrarTodosLosUsuariosConServicios();
	});

	Auxiliares.llenarHorarios(0, 24, 'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1');
	Auxiliares.llenarHorarios(0, 24, 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno1');
	Auxiliares.llenarHorarios(0, 24, 'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2');
	Auxiliares.llenarHorarios(0, 24, 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno2');
	Auxiliares.llenarHorarios(0, 24, 'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno3');
	Auxiliares.llenarHorarios(0, 24, 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno3');

});


/**
 * Clase encargada de comunicarse con el controller para peticiones AJAX
 */
class AJAX
{

	/* ------ ------ ------ / ------ ------ ------ / ------ ------ ------ ////// - /GET/ - ////// ------ ------ ------ / ------ ------ ------ / ------ ------ ------ */

	static logIn()
	{
		if(Control.logIn())
			$.ajax(
			{
				type: 'POST',
				url: 'controllers/cAJAX.php',
				data:
				{
					funcion: 'logIn',
					parametros:
					{
						usuario:	$('#usuarioLogIn').val(),
						password:	$('#passwordLogIn').val()
					}
				},
				dataType: 'JSON',
				success: e => Callback.logIn(e)
			});
	}

	static buscarCedula(parametro)
	{
		if(Control.buscarCedula(parametro))
			$.ajax(
			{
				type: 'POST',
				url: 'controllers/cAJAX.php',
				data:
				{
					funcion: 'buscarCedula',
					parametros:
					{
						cedula: $('#' + parametro + 'BuscarCedula').val()
					}
				},
				dataType: 'JSON',
				success: (e) => {Callback.buscarCedula(e, parametro)}
			});
	}

	static buscarNYA()
	{
			$.ajax(
			{
				type: 'POST',
				url: 'controllers/cAJAX.php',
				data:
				{
					funcion: 'buscarNYA',
					parametros:
					{
						NYA: $('#tomarPedidoBuscarNYA').val()
					}
				},
				dataType: 'JSON',
				success: (data) => {
					$('#modalTomarPedido').css('display', 'block');
					$('#tablaTomaPedido').empty();

					let table = `<table>
					<thead>
					<tr>
					<td>Nombre</td>
					<td>Cedula</td>
					<td>Telefono</td>
					<td>Accion</td>
					</tr>
					<thead>
						<tbody>
						`;

						let e = [];

					$.each(data.datos ,function(i ,val){

								 e = [{
									correcto:true,
									nombre:val.nombre,
									telefono:val.telefono,
									cedula:val.cedula }];

									e = JSON.stringify(e);

							table +=` <tr>
							<td>`+val.nombre+`</td>
							<td>`+val.cedula+`</td>
							<td>`+val.telefono+`</td>
							<input type="hidden" id="`+val.cedula+`BuscarCedula" value="`+val.cedula+`">
							<td><button onclick="AJAX.buscarCedula(`+val.cedula+`);$('#modalTomarPedido').css('display', 'none');" class="btnDiego">Tomar Pedido</button></td>
							</tr>`;
					});
					
					table += ` 
					</tbody>
					<table>
				`;

					$('#tablaTomaPedido').append(table);

				}
			});
	}

	static serviciosDelCliente(cedula, selectId, predeterminado = '')
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'serviciosDelCliente',
				parametros:
				{
					cedula: cedula
				}
			},
			dataType: 'JSON',
			success: e => Callback.llenarSelect(e, selectId, predeterminado)
		});
	}

	static llenarLugaresDeInternacion(selectId, predeterminado = '')
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'llenarLugaresDeInternacion'
			},
			dataType: 'JSON',
			success: e => Callback.llenarSelect(e, selectId, predeterminado)
		});
	}

	static llenarDepartamentos(selectId, predeterminado = '')
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'llenarDepartamentos'
			},
			dataType: 'JSON',
			success: e => Callback.llenarSelect(e, selectId, predeterminado)
		});
	}

	static mostrarProductos(cedula, selectId)
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'mostrarProductos',
				parametros:
				{
					cedula: cedula
				}
			},
			dataType: 'JSON',
			success: e => Callback.mostrarProductos(e, selectId)
		});
	}

	static mostrarCobranza(cedula, selectId)
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'mostrarCobranza',
				parametros:
				{
					cedula: cedula
				}
			},
			dataType: 'JSON',
			success: e => Callback.mostrarCobranza(e, selectId)
		});
	}

	static mostrarServicios(cedula, selectId)
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'mostrarServicios',
				parametros:
				{
					cedula: cedula
				}
			},
			dataType: 'JSON',
			success: e => Callback.mostrarServicios(e, selectId)
		});
	}

	static posiblesReintegros(cedula)
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'posiblesReintegros',
				parametros:
				{
					cedula: cedula
				}
			},
			dataType: 'JSON',
			success: e => Callback.posiblesReintegros(e)
		});
	}

	static formularioReintegro()
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'formularioReintegro',
				parametros:
				{
					id: id
				}
			},
			dataType: 'JSON',
			success: e => Callback.formularioReintegro(e)
		});
	}

	//COORDINACIÓN DE SERVICIOS

	static mostrarDatosBarraCoordinacion()
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'mostrarDatosBarraCoordinacion'
			},
			dataType: 'JSON',
			success: e => Callback.mostrarDatosBarraCoordinacion(e)
		});
	}

	static coordinacionDeServiciosModificarDatos()
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'coordinacionDeServiciosModificarDatos'
			},
			dataType: 'JSON',
			success: e => Callback.coordinacionDeServiciosModificarDatos(e)
		});
	}

	static coordinacionDeServiciosPendientes(dia)
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'coordinacionDeServiciosPendientes',
				parametros:
				{
					dia: dia
				}
			},
			dataType: 'JSON',
			success: e => Callback.coordinacionDeServiciosPendientes(e)
		});
	}

	static coordinacionDeServiciosActivos(tipo)
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'coordinacionDeServiciosActivos',
				parametros:
				{
					tipo: tipo
				}
			},
			dataType: 'JSON',
			success: e => Callback.coordinacionDeServiciosActivos(e)
		});
	}

	static buscarAcompanhanteNavBar()
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'buscarAcompanhanteNavBar',
				parametros:
				{
					tipo: tipo
				}
			},
			dataType: 'JSON',
			success: e => Callback.buscarAcompanhanteNavBar(e)
		});
	}

	static traerInfoAcompanhante()
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'traerInfoAcompanhante',
				parametros:
				{
					cedula: $('#buscarAcompanhanteNavBar').val()
				}
			},
			dataType: 'JSON',
			success: e => Callback.traerInfoAcompanhante(e)
		});
	}

	static traerInfoCliente()
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'traerInfoCliente',
				parametros:
				{
					cedula: $('#buscarClienteNavBar').val()
				}
			},
			dataType: 'JSON',
			success: e => Callback.traerInfoCliente(e)
		});
	}

	static mostrarCoordinacionDeServiciosAsignarAcompanhante(id)
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'mostrarCoordinacionDeServiciosAsignarAcompanhante',
				parametros:
				{
					id : id
				}
			},
			dataType: 'JSON',
			success: e => Callback.mostrarCoordinacionDeServiciosAsignarAcompanhante(e, id)
		});
	}

	static insertarCoordinacionDeServiciosAsignarAcompanhante(id)
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'insertarCoordinacionDeServiciosAsignarAcompanhante',
				parametros:
				{
					id : id
				}
			},
			dataType: 'JSON',
			success: e => Callback.insertarCoordinacionDeServiciosAsignarAcompanhante(e)
		});
	}

	static mostrarTodosLosUsuariosConServicios()
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'mostrarTodosLosUsuariosConServicios'
			},
			dataType: 'JSON',
			success: e => Callback.mostrarTodosLosUsuariosConServicios(e)
		});
	}



	/* ------ ------ ------ / ------ ------ ------ / ------ ------ ------ ////// - /PUT/ - ////// ------ ------ ------ / ------ ------ ------ / ------ ------ ------ */

	static tomarPedido()
	{
		if(Control.tomarPedido())
			$.ajax(
			{
				type: 'POST',
				url: 'controllers/cAJAX.php',
				data:
				{
					funcion: 'tomarPedido',
					parametros:
					{
						nombre				: $('#tomarPedidoNombre').val(),
						cedula				: $('#tomarPedidoCedula').val(),
						telefono			: $('#tomarPedidoTelefono').val(),
						lugarInternacion	: $('#tomarPedidoLugarInternacion').val(),
						departamento		: $('#tomarPedidoDepartamento').val(),
						servicio			: $('#tomarPedidoTipoDeServicio :selected').text(),
						observaciones		: $('#tomarPedidoObservaciones').val(),
						sala				: $('#tomarPedidoSala').val(),
						cama				: $('#tomarPedidoCama').val(),
						piso				: $('#tomarPedidoPiso').val(),
						horasDeServicio		: $('#tomarPedidoHorasServicio').val(),
						fechaInicio			: $('#tomarPedidoFechaInicio').val(),
						horaInicio			: $('#tomarPedidoHoraInicio').val()
					}
				},
				dataType: 'JSON',
				success: e => Callback.tomarPedido(e)
			});
	}

	static tomarPedidoNoSocio()
	{
		if(Control.tomarPedidoNoSocio())
			$.ajax(
			{
				type: 'POST',
				url: 'controllers/cAJAX.php',
				data:
				{
					funcion: 'tomarPedido',
					parametros:
					{
						nombre				: $('#tomarPedidoNombreNoSocio').val(),
						cedula				: $('#tomarPedidoCedulaNoSocio').val(),
						telefono			: $('#tomarPedidoTelefonoNoSocio').val(),
						lugarInternacion	: $('#tomarPedidoLugarInternacionNoSocio').val(),
						departamento		: '',
						servicio			: $('#tomarPedidoTipoDeServicioNoSocio :selected').text(),
						observaciones		: $('#tomarPedidoObservacionesNoSocio').val(),
						sala				: $('#tomarPedidoSalaNoSocio').val(),
						cama				: $('#tomarPedidoCamaNoSocio').val(),
						piso				: $('#tomarPedidoPisoNoSocio').val(),
						horasDeServicio		: $('#tomarPedidoHorasServicioNoSocio').val(),
						fechaInicio			: $('#tomarPedidoFechaInicioNoSocio').val(),
						horaInicio			: $('#tomarPedidoHoraInicioNoSocio').val()
					}
				},
				dataType: 'JSON',
				success: e => Callback.tomarPedido(e)
			});
	}

	static coordinacionDeServiciosModificarDatosFinal()
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'coordinacionDeServiciosModificarDatosFinal',
				parametros:
				{
					id					: $('#coordinacionDeServiciosModificarDatosFinalID').val(),
					telefono			: $('#coordinacionDeServiciosModificarDatosFinalTelefono').val(),
					lugarInternacion	: $('#coordinacionDeServiciosModificarDatosFinalLugarInternacion').val(),
					servicio			: $('#coordinacionDeServiciosModificarDatosFinalTipoDeServicio :selected').text(),
					observaciones		: $('#coordinacionDeServiciosModificarDatosFinalObservaciones').val(),
					sala				: $('#coordinacionDeServiciosModificarDatosFinalSala').val(),
					cama				: $('#coordinacionDeServiciosModificarDatosFinalCama').val(),
					piso				: $('#coordinacionDeServiciosModificarDatosFinalPiso').val(),
					horasDeServicio		: $('#coordinacionDeServiciosModificarDatosFinalHorasServicio').val(),
					fechaInicio			: $('#coordinacionDeServiciosModificarDatosFinalFechaInicio').val(),
					horaInicio			: $('#coordinacionDeServiciosModificarDatosFinalHoraInicio').val()
				}
			},
			dataType: 'JSON',
			success: e => Callback.coordinacionDeServiciosModificarDatosFinal(e)
		});
	}

	static guardarCoordinacionDeServiciosAsignarAcompanhante()
	{
		if(Control.guardarCoordinacionDeServiciosAsignarAcompanhante())
			$.ajax(
			{
				type: 'POST',
				url: 'controllers/cAJAX.php',
				data:
				{
					funcion: 'guardarCoordinacionDeServiciosAsignarAcompanhante',
					parametros:
					{
						id					: $('#coordinacionDeServiciosAsignarAcompanhanteID').val(),
						referencia			: $('#coordinacionDeServiciosAsignarAcompanhanteReferencia').val(),
						fecha				: $('#coordinacionDeServiciosAsignarAcompanhanteFecha').val(),
						cantidadTurnos		: $('#coordinacionDeServiciosAsignarAcompanhanteTurnos').val(),
						nombreAcompanhanteT1: $('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno1').val(),
						cedulaAcompanhanteT1: $('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno1').val(),
						desdeT1				: $('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1 :selected').text(),
						hastaT1				: $('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno1 :selected').text(),
						nombreAcompanhanteT2: $('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno2').val(),
						cedulaAcompanhanteT2: $('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno2').val(),
						desdeT2				: $('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2 :selected').text(),
						hastaT2				: $('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno2 :selected').text(),
						nombreAcompanhanteT3: $('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno3').val(),
						cedulaAcompanhanteT3: $('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno3').val(),
						desdeT3				: $('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno3 :selected').text(),
						hastaT3				: $('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno3 :selected').text()
					}
				},
				dataType: 'JSON',
				success: e => Callback.guardarCoordinacionDeServiciosAsignarAcompanhante(e)
			});
	}

	static informacionDeServicio(id)
	{
		$.ajax(
		{
			type: 'POST',
			url: 'controllers/cAJAX.php',
			data:
			{
				funcion: 'informacionDeServicio',
				parametros:
				{
					id: id
				}
			},
			dataType: 'JSON',
			success: e => Callback.informacionDeServicio(e)
		});
	}
}

class Callback
{
	static logIn(e)
	{
		if(e.correcto)
			location.reload(true);
		else
			Visual.ensuciar(['usuarioLogIn', 'passwordLogIn'], 'Usuario o contraseña incorrecta.');
	}

	static mostrarProductos(e, selectId)
	{
		if(e.error)
			alert('>:(');
		else
		{
			$('#' + selectId).empty();
			let elementos = '';
			e.forEach(el =>
			{
				elementos +=
				`<tr style="text-align: center;">
					<td>` + el.id + `</td>
					<td>` + el.servicio + `</td>
					<td>` + el.horas + `</td>
					<td>` + el.fecha_afiliacion + `</td>
				</tr>`;
			});
			$(elementos).appendTo($('#' + selectId));
		}
	}

	static mostrarCobranza(e, selectId)
	{
		if(e.error)
			alert('>:(');
		else
		{
			$('#' + selectId).empty();
			let elementos = '';
			e.forEach(el =>
			{
				elementos +=
				`<tr style="text-align: center;">
					<td>` + el.fecha + `</td>
					<td>Cobrado</td>
				</tr>`;
			});
			$(elementos).appendTo($('#' + selectId));
		}
	}

	static mostrarServicios(e, selectId)
	{
		if(e.error)
			alert('>:(');
		else if(e.sinRegistros)
		{
			$('#' + selectId).empty();
			let elementos =
			`
			<tr style="color: red; text-align: center;">
				<th>~</th>
				<th>Sin</th>
				<th>registros</th>
				<th>disponibles</th>
				<th>~</th>
			</tr>`;
			$(elementos).appendTo($('#' + selectId));
		}
		else
		{
			$('#' + selectId).empty();
			let elementos = '';
			e.forEach(el =>
			{
				elementos +=
				`<tr style="text-align: center;">
					<td>` + el.id		+ `</td>
					<td>` + el.desde	+ `</td>
					<td>` + el.hasta	+ `</td>
					<td>` + el.cantidad	+ `</td>
					<td>` + el.turnos	+ `</td>
				</tr>`;
			});
			$(elementos).appendTo($('#' + selectId));
		}
	}

	static buscarCedula(e, parametro)
	{	
		if(e.correcto)
		{
			$('#contenedor-main-cedula').css('display', 'none');
			$('#tomarPedidoDetalles').css('display', 'block');
			$('#tomarPedidoDetalles2').css('display', 'block');
			if(parametro == 'tomarPedido' || Number.isInteger(parametro) )
			{
				Visual.limpiar(
				[
					'tomarPedidoNombre',
					'tomarPedidoCedula',
					'tomarPedidoTelefono',
					'tomarPedidoLugarInternacion',
					'tomarPedidoTipoDeServicio',
					'tomarPedidoHorasServicio',
					'tomarPedidoSala',
					'tomarPedidoCama',
					'tomarPedidoPiso',
					'tomarPedidoFechaInicio',
					'tomarPedidoHoraInicio',
					'tomarPedidoObservaciones'
				]);
				AJAX.mostrarProductos($('#tomarPedidoBuscarCedula').val(), 'tomaPedidoProductosTbody');
				AJAX.mostrarCobranza($('#tomarPedidoBuscarCedula').val(), 'tomaPedidoCobranzasTbody');
				AJAX.mostrarServicios($('#tomarPedidoBuscarCedula').val(), 'tomaPedidoServiciosTbody');
				AJAX.serviciosDelCliente($('#tomarPedidoBuscarCedula').val(), 'tomarPedidoTipoDeServicio');
				$('#tomarPedidoNombre').val(e.nombre);
				$('#tomarPedidoCedula').val($('#'+parametro+'BuscarCedula').val());
				$('#tomarPedidoTelefono').val(e.telefono);
			}
		}
		else if(e.sinFinalizar)
		{
			if(confirm("El socio ingresado tiene un pedido sin finalizar\n¿Desea ir al registro?"))
			{
				Visual.mostrarDivPrincipal('coordinacionDeServiciosDiv');
				$('#coordinacionDeServiciosAsignarAcompanhanteReferencia').val(e.id);
				AJAX.mostrarCoordinacionDeServiciosAsignarAcompanhante(e.id);
			}
		}
		else if(e.noSocio)
		{
			if(confirm("No es socio de Vida.\n¿Desea ingresarle un servicio igualmente?"))
			{
				Visual.limpiar(
				[
					'tomarPedidoNombreNoSocio',
					'tomarPedidoCedulaNoSocio',
					'tomarPedidoTelefonoNoSocio',
					'tomarPedidoLugarInternacionNoSocio',
					'tomarPedidoTipoDeServicioNoSocio',
					'tomarPedidoHorasServicioNoSocio',
					'tomarPedidoSalaNoSocio',
					'tomarPedidoCamaNoSocio',
					'tomarPedidoPisoNoSocio',
					'tomarPedidoFechaInicioNoSocio',
					'tomarPedidoHoraInicioNoSocio',
					'tomarPedidoObservacionesNoSocio'
				]);
				$('#tomarPedidoCedulaNoSocio').val($('#tomarPedidoBuscarCedula').val());
				$('#contenedor-main-cedula').css('display', 'none');
				$('#tomarPedidoDetallesNoSocio').css('display', 'block');
			}
			else
				Visual.ensuciar(parametro + 'BuscarCedula', 'No es socio de Vida.');
		}
		else if(e.error)
		{
			if(e.query)
				alert('Ha ocurrido un error crítico, por favor comuníquese con el administrador.');
		}
	}

	static posiblesReintegros(e)
	{
		if(e.correcto)
		{
			if(e.sinRegistros)
				alert('Este usuario no tiene ningún servicio que habilite el reintegro.');
			else
			{
				let codigoHTML =
				`
				<table>
					<tbody>
						<th>ID</th>
						<th>Desde</th>
						<th>Hasta</th>
						<th>Cantidad de días</th>
						<th>Turnos</th>
						<th>Seleccionar</th>
					</tbody>
					<tbody>
				`;

				if(Array.isArray(e.datos))
					e.datos.forEach(e =>
					{
						codigoHTML +=
						`
						<tr style="text-align: center;">
							<td> ` + e.id		+ `</td>
							<td> ` + e.desde	+ `</td>
							<td> ` + e.hasta	+ `</td>
							<td> ` + e.cantidad	+ `</td>
							<td> ` + e.turnos	+ `</td>
							<td><input type="button" value="Seleccionar" onclick="AJAX.formularioReintegro(` + e.id + `)"></td>
						</tr>
						`;
					});
				else
					codigoHTML +=
					`
					<tr style="text-align: center;">
						<td> ` + e.datos.id		+ `</td>
						<td> ` + e.datos.desde	+ `</td>
						<td> ` + e.datos.hasta	+ `</td>
						<td> ` + e.datos.cantidad	+ `</td>
						<td> ` + e.datos.turnos	+ `</td>
						<td><input type="button" value="Seleccionar" onclick="AJAX.formularioReintegro(` + e.datos.id + `)"></td>
					</tr>
					`;

				codigoHTML += '</tbody></table>';
				Visual.mostrarModal('Seleccione servicio para reintegro', '', true, codigoHTML);
			}
		}
		else if(e.error)
		{
			if(e.sinReintegro)
				alert('Ha ocurrido un error.');
			else if(e.query)
				alert('Ha ocurrido un error en la query: ' + e.nroQuery);
		}
	}

	static tomarPedido(e)
	{
		if(e.correcto)
		{
			alert("La petición se ha concretado de forma satisfactoria.\nEl código de control es: " + e.nroControl);
			location.reload(true);
		}
		else if(e.error)
		{
			
			if(e.query){
				alert('Ha ocurrido un error en la query: ' + e.nroQuery);
			}else{
				if(e.departamentoVacio){
					alert('El departamento de lugar de Internación no debe estar vacio');
				}
			}

		}
	}

	static llenarSelect(e, selectId, predeterminado)
	{
		if(e.correcto)
		{
			$('#' + selectId).empty();
			let options =  '<option value="0">Seleccione una opción</option>';

			e.datos.forEach(el =>
			{
				options += '<option value="' + el.id + '">' + el.nombre + '</option>'
			});

			$(options).appendTo('#' + selectId);

			if(predeterminado !== '')
				$('#' + selectId + ' option').each((i, e) =>
				{
					if(predeterminado === $(e).text() || predeterminado === $(e).val())
						$(e).prop('selected', true);
				});
		}
		else if(e.error)
		{
			alert('Ha ocurrido un error crítico.');
			location.reload(true);
		}
		else
			location.reload(true);
	}

	static mostrarDatosBarraCoordinacion(e)
	{
		if(e.correcto)
		{
			$('.badgeServiciosActivosCompletos').text(e.badgeServiciosActivosCompletos);
			$('.badgeServiciosActivosPendientes').text(e.badgeServiciosActivosPendientes);
			$('.badgeServiciosSinAcompanhante').text(e.badgeServiciosSinAcompanhante);
			$('#badgeServiciosTotal').text(e.badgeServiciosActivosCompletos + e.badgeServiciosActivosPendientes + e.badgeServiciosSinAcompanhante);
			$('.badgeTodosLosServiciosActuales').text($('#badgeServiciosTotal').text());

			$('.badgeTurnosPendientesAyer').text(e.badgeTurnosPendientesAyer);
			$('.badgeTurnosPendientesHoy').text(e.badgeTurnosPendientesHoy);
			$('.badgeTurnosPendientesManhana').text(e.badgeTurnosPendientesManhana);
			$('#badgeTurnosTotal').text(e.badgeTurnosPendientesAyer + e.badgeTurnosPendientesHoy + e.badgeTurnosPendientesManhana);
		}
		else if(e.error)
			alert('Ha ocurrido un error en la query: ' + e.nroQuery);
	}

	////////////////////////////////// TURNOS

	static coordinacionDeServiciosPendientes(e)
	{
		if(e.error)
			alert('Error crítico.');
		else if(e.sinRegistros)
			alert('No hay pendientes.');
		else
		{
			Auxiliares.cambiarTituloActual();
			Auxiliares.destruirTodasLasTablas();
			$('#coordinacionDeServiciosPendientesTbody').empty();

			let lineasTabla = '';

			e.forEach(e =>
			{
				lineasTabla +=
				`<tr>
					<td>` + e.turnos			+ `</td>
					<td>` + e.fecha				+ `</td>
					<td>` + e.nombreT1 			+ `</td>
					<td>` + e.cedulaT1			+ `</td>
					<td>` + e.horaInicioT1		+ `</td>
					<td>` + e.horaFinalT1		+ `</td>
					<td>` + e.nombreT2 			+ `</td>
					<td>` + e.cedulaT2			+ `</td>
					<td>` + e.horaInicioT2		+ `</td>
					<td>` + e.horaFinalT2		+ `</td>
					<td>` + e.nombreT3 			+ `</td>
					<td>` + e.cedulaT3			+ `</td>
					<td>` + e.horaInicioT3		+ `</td>
					<td>` + e.horaFinalT3		+ `</td>
					<td><input type="button" value="Ir" onclick="Auxiliares.irDirectamenteAlServicio(` + e.id + `, ` + e.idinfo + `)"></td>
				</tr>`;
			});

			$(lineasTabla).appendTo($('#coordinacionDeServiciosPendientesTbody'));
			$('#coordinacionDeServiciosPendientesTable').DataTable(configuracionDataTable);

			$('#coordinacionDeServiciosPendientesTable').css('display', 'block');
		}
	}

	static coordinacionDeServiciosActivos(e)
	{
		if(e.error)
			alert('Error crítico.');
		else if(e.sinRegistros)
			alert('No hay pendientes.');
		else
		{
			Auxiliares.destruirTodasLasTablas();
			Auxiliares.cambiarTituloActual();
			$('#coordinacionDeServiciosActivosTbody').empty();

			let lineasTabla = '';

			e.forEach(e =>
			{
				lineasTabla +=
				`<tr>
					<td>` + e.id			+ `</td>
					<td>` + e.cedula		+ `</td>
					<td>` + e.nombre		+ `</td>
					<td><input type="button" value="detalles" class="detalles" onclick="Visual.mostrarModal('Observación', ` + '`' + e.observacion + '`' + `)"></td>
					<td>` + e.fecha			+ `</td>
					<td>` + e.hora			+ `</td>
					<td>` + e.horas			+ `</td>
					<td>` + e.telefono		+ `</td>
					<td>` + e.lugar 		+ `</td>
					<td>` + e.piso			+ `</td>
					<td>` + e.sala			+ `</td>
					<td>` + e.cama			+ `</td>
					` +
					//<td>` + e.acompanhante	+ `</td> +
					`
					<td>` + e.fechaCarga 	+ `</td>
					<td>` + e.tiempoPedido	+ `</td>
					<td><input type="button" value="Coordinar" class="detalles" onclick="AJAX.mostrarCoordinacionDeServiciosAsignarAcompanhante(` + e.id + `); $('#coordinacionDeServiciosAsignarAcompanhanteReferencia').val(` + e.id + `);" style="width: 100%;"></td>
				</tr>`;
			});

			$(lineasTabla).appendTo($('#coordinacionDeServiciosActivosTbody'));
			$('#coordinacionDeServiciosActivosTable').DataTable(configuracionDataTable);

			$('#coordinacionDeServiciosActivosTable').css('display', 'block');
		}
	}

	////////////////////////////////// SERVICIOS

	static coordinacionDeServiciosModificarDatos(e)
	{
		if(e.error)
		{

		}
		else if(e.sinRegistros)
			alert('No hay pendientes.');
		else
		{
			Auxiliares.cambiarTituloActual('Coordinación de servicios - Modificar datos del toma pedidos');
			Auxiliares.destruirTodasLasTablas();
			$('#coordinacionDeServiciosModificarDatosTbody').empty();

			let lineasTabla = '';

			e.forEach(e =>
			{
				lineasTabla +=
				`<tr>
					<td>` + e.id			+ `</td>
					<td>` + e.cedula		+ `</td>
					<td>` + e.nombre		+ `</td>
					<td><input type="button" value="detalles" class="detalles" onclick="Visual.mostrarModal('Observación', ` + '`' + e.observacion + '`' + `)"></td>
					<td>` + e.fecha			+ `</td>
					<td>` + e.hora			+ `</td>
					<td>` + e.horaPorDia	+ `</td>
					<td>` + e.telefono		+ `</td>
					<td>` + e.lugar 		+ `</td>
					<td>` + e.piso			+ `</td>
					<td>` + e.sala			+ `</td>
					<td>` + e.cama			+ `</td>
					<td>` + e.tipo			+ `</td>
					<td><input type="button" value="modificar" class="modificar" onclick="Visual.coordinacionDeServiciosModificarDatosFinal(` + e.id + ', ' + e.cedula + ', \'' + e.nombre + '\', \`' + e.observacion + '\`, \'' + e.fecha + '\', ' + e.hora + ', ' + e.horaPorDia + ', \'' + e.telefono + '\', \'' + e.lugar + '\', ' + e.piso + ', \'' + e.sala + '\', \'' + e.cama + '\', \'' + e.tipo + '\'' + `)" style="width: 100%;"></td>
				</tr>`;
			});

			$(lineasTabla).appendTo($('#coordinacionDeServiciosModificarDatosTbody'));
			$('#coordinacionDeServiciosModificarDatosTable').DataTable(configuracionDataTable);

			$('#coordinacionDeServiciosModificarDatosTable').css('display', 'block');
		}
	}

	static coordinacionDeServiciosModificarDatosFinal(e)
	{
		if(e.error)
		{
			if(e.query)
				alert('Ha ocurrido un error en la query: ' + e.nroQuery);
		}
		else if(e.correcto)
		{
			alert('Registro modificado de forma correcta.');
			location.reload(true);
		}
	}

	static traerInfoAcompanhante(e)
	{
		if(e.error)
		{
			if(e.query)
				alert('Ha ocurrido un error en la query: ' + e.nroQuery);
		}
		else if(e.sinRegistros)
			alert('Cédula sin registros.');
		else
		{
			Auxiliares.cambiarTituloActual('Coordinación de servicios - Información de acompañante');
			Auxiliares.destruirTodasLasTablas();

			$('#coordinacionDeServiciosAcompanhantesNombre').val(e.nombre);
			$('#coordinacionDeServiciosAcompanhantesCedula').val($('#buscarAcompanhanteNavBar').val());
			$('#coordinacionDeServiciosAcompanhantesNacimiento').val(e.nacimiento);
			$('#coordinacionDeServiciosAcompanhantesTelefono').val(e.telefono);
			$('#coordinacionDeServiciosAcompanhantesDepartamento').val(e.departamento);
			$('#coordinacionDeServiciosAcompanhantesDireccion').val(e.direccion);
			$('#coordinacionDeServiciosAcompanhantesIngreso').val(e.ingreso);
			$('#coordinacionDeServiciosAcompanhantesEnServicio').val((e.enServicio) ? 'Sí' : 'No');
			$('#coordinacionDeServiciosAcompanhantesUltimoDia').val(e.ultimoDia);
			$('#coordinacionDeServiciosAcompanhantesServicioFuturo').val((e.servicioFuturo === '') ? 'Sin próximo servicio' : e.servicioFuturo);
			$('#coordinacionDeServiciosAcompanhantesHorasMes').val(e.horasMes);
			$('#coordinacionDeServiciosAcompanhantesHorasTotales').val(e.horasTotales);

			$('#coordinacionDeServiciosAcompanhantesDiv').css('display', 'block');
		}
	}

	static traerInfoCliente(e)
	{
		if(e.sinRegistros)
			alert('Sin registros.');
		else
		{
			let codigoHTML =
			`
			<div>
				<table>
					<thead>
						<tr>
							<th>ID:</th>
							<th>Lugar:</th>
							<th>Horas:</th>
							<th>Estado:</th>
							<th>Ir al servicio:</th>
						</tr>
					</thead>
					<tbody>
			`;
			e.datos.forEach(e =>
			{
				let estado;
				if(e.activo)
					estado = 'Activo';
				else if(e.cancelado)
					estado = 'Cancelado';
				else if(e.finalizado)
					estado = 'Finalizado';
				else if(e.pendiente)
					estado = 'Pendiente';
				codigoHTML +=
				`
				<tr>
					<td>${e.id}</td>
					<td>${e.lugar}</td>
					<td>${e.horas}</td>
					<td>${estado}</td>
					<td><input type="button" value="Ir al servicio">
				</tr>
				`;
			});

			codigoHTML += '</tbody></div>';
			Visual.mostrarModal('Info cliente', '', true, codigoHTML);
		}
	}

	static mostrarCoordinacionDeServiciosAsignarAcompanhante(e, id)
	{
		Auxiliares.destruirTodasLasTablas();

		if(e.error)
		{
			if(e.query)
				alert('Ha ocurrido un error en la query: ' + e.nroQuery);
		}
		else if(e.correcto)
		{
			AJAX.informacionDeServicio(id);
			$('#numeroDelServicioSeleccionado').text(id);
			$('#coordinacionDeServiciosAsignarAcompanhanteReferencia').val();

			$('#coordinacionDeServiciosAsignarAcompanhanteTitulo').val('Crear registro nuevo:');
			$('#coordinacionDeServiciosAsignarAcompanhanteID').val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno1').val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno1').val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1').val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno1').val('');

			$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno2').val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno2').val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2').val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno2').val('');

			$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno3').val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno3').val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno3').val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno3').val('');

			if(e.sinRegistros)
				$('#coordinacionDeServiciosAcompanhantesSinRegistros').show();
			else
			{
				$('#coordinacionDeServiciosAcompanhantesHistorialTbody').empty();
				let nuevaLinea = '';
				if(Array.isArray(e.datos))
					e.datos.forEach(e =>
					{
						let boton = (e.modificar)
							? '<input type="button" value="Modificar" onclick="AJAX.insertarCoordinacionDeServiciosAsignarAcompanhante(' + e.id + ')">'
							: '<input type="button" value="No disponible" disabled>';
						nuevaLinea +=
						`<tr style="text-align: center;">
							<td>` + e.dia + `</td>
							<td>` + e.turnos + `</td>
							<td>` + e.acompanhanteT1 + `</td>
							<td>` + e.cedulaT1 + `</td>
							<td>` + e.hIT1 + `</td>
							<td>` + e.hFT1 + `</td>
							<td>` + e.acompanhanteT2 + `</td>
							<td>` + e.cedulaT2 + `</td>
							<td>` + e.hIT2 + `</td>
							<td>` + e.hFT2 + `</td>
							<td>` + e.acompanhanteT3 + `</td>
							<td>` + e.cedulaT3 + `</td>
							<td>` + e.hIT3 + `</td>
							<td>` + e.hFT3 + `</td>
							<td>` + boton + `</td>
						</tr>`;
					});
				else
				{
					let boton = (e.datos.modificar)
						? '<input type="button" value="Modificar" onclick="AJAX.insertarCoordinacionDeServiciosAsignarAcompanhante(' + e.datos.id + ')">'
						: '<input type="button" value="No disponible" disabled>';
					nuevaLinea +=
					`<tr style="text-align: center;">
						<td>` + e.datos.id + `</td>
						<td>` + e.datos.dia + `</td>
						<td>` + e.datos.turnos + `</td>
						<td>` + e.datos.acompanhanteT1 + `</td>
						<td>` + e.datos.cedulaT1 + `</td>
						<td>` + e.datos.hIT1 + `</td>
						<td>` + e.datos.hFT1 + `</td>
						<td>` + e.datos.acompanhanteT2 + `</td>
						<td>` + e.datos.cedulaT2 + `</td>
						<td>` + e.datos.hIT2 + `</td>
						<td>` + e.datos.hFT2 + `</td>
						<td>` + e.datos.acompanhanteT3 + `</td>
						<td>` + e.datos.cedulaT3 + `</td>
						<td>` + e.datos.hIT3 + `</td>
						<td>` + e.datos.hFT3 + `</td>
						<td>` + boton + `</td>
					</tr>`;
				}

				$(nuevaLinea).appendTo('#coordinacionDeServiciosAcompanhantesHistorialTbody');
				$('#coordinacionDeServiciosAcompanhantesHistorialTable').DataTable(configuracionDataTable);
				$('#coordinacionDeServiciosAcompanhantesHistorialTable').show();
			}

			$('#coordinacionDeServiciosAsignarAcompanhante').show();
		}
	}

	static insertarCoordinacionDeServiciosAsignarAcompanhante(e)
	{
		if(e.error)
		{
			if(e.query)
				alert('Ha ocurrido un error en la query: ' + e.nroQuery);
		}
		else if(e.correcto)
		{
			Visual.limpiar(
			[
				'coordinacionDeServiciosAsignarAcompanhanteFecha',
				'coordinacionDeServiciosAsignarAcompanhanteNombreTurno1', 'coordinacionDeServiciosAsignarAcompanhanteCedulaTurno1',
				'coordinacionDeServiciosAsignarAcompanhanteNombreTurno2', 'coordinacionDeServiciosAsignarAcompanhanteCedulaTurno2',
				'coordinacionDeServiciosAsignarAcompanhanteNombreTurno3', 'coordinacionDeServiciosAsignarAcompanhanteCedulaTurno3'
			],
			true);

			Visual.limpiar(
			[
				'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno1',
				'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno2',
				'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno3', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno3'
			]);

			$('#coordinacionDeServiciosAsignarAcompanhanteTitulo').text('Modificar el registro nro: ' + e.id);
			$('#coordinacionDeServiciosAsignarAcompanhanteID').val(e.id);
			$('#coordinacionDeServiciosAsignarAcompanhanteFecha').val(e.dia);
			$('#coordinacionDeServiciosAsignarAcompanhanteTurnos').val(e.turnos);
			$('#coordinacionDeServiciosAsignarAcompanhanteTurnos').change();
			switch (e.turnos)
			{
				case 1:
					$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno1').val(e.acompanhanteT1);
					$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno1').val(e.cedulaT1);
					$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1').val(e.hIT1);
					$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno1').val(e.hFT1);
					break;
				case 2:
					$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno1').val(e.acompanhanteT1);
					$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno1').val(e.cedulaT1);
					$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1').val(e.hIT1);
					$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno1').val(e.hFT1);

					$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno2').val(e.acompanhanteT2);
					$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno2').val(e.cedulaT2);
					$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2').val(e.hIT2);
					$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno2').val(e.hFT2);
					break;
				case 3:
					$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno1').val(e.acompanhanteT1);
					$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno1').val(e.cedulaT1);
					$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1').val(e.hIT1);
					$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno1').val(e.hFT1);

					$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno2').val(e.acompanhanteT2);
					$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno2').val(e.cedulaT2);
					$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2').val(e.hIT2);
					$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno2').val(e.hFT2);

					$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno3').val(e.acompanhanteT3);
					$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno3').val(e.cedulaT3);
					$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno3').val(e.hIT3);
					$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno3').val(e.hFT3);
					break;
			}
			$('#coordinacionDeServiciosAsignarAcompanhanteDiv').show();
			window.scrollTo(0, 0);
		}
	}

	static mostrarTodosLosUsuariosConServicios(e)
	{
		Auxiliares.destruirTodasLasTablas();
		$('#coordinacionDeTodosLosServiciosActualesTbody').empty()
		let nuevaLinea	= '';
		let	tr 			= '';
		e.forEach(e =>
		{
			if(e.sinAcompanhante)
				tr = '<tr style="background: #FFD6D6!important">';
			else if(e.pendiente)
				tr = '<tr style="background: #F9FFCF!important">';
			else if(e.completo)
				tr = '<tr style="background: #D2FFCF!important">';

			nuevaLinea +=
			tr + `
				<td>` + e.id + `</td>
				<td>` + e.cedula + `</td>
				<td>` + e.nombre + `</td>
				<td><input type="button" value="detalles" class="detalles" onclick="Visual.mostrarModal('Observación', ` + '`' + e.observacion + '`' + `)"></td>
				<td>` + e.fecha + `</td>
				<td>` + e.lugar + `</td>
				<td>Más info</td>
			</tr>`;
		});

		$(nuevaLinea).appendTo('#coordinacionDeTodosLosServiciosActualesTbody');
		$('#coordinacionDeTodosLosServiciosActualesTable').DataTable(configuracionDataTable);
		$('#coordinacionDeTodosLosServiciosActualesTable').css('display', 'block');
	}

	static informacionDeServicio(e)
	{
		window.localStorage.removeItem(' ');
		window.localStorage.setItem('detallesDeServicio', JSON.stringify(e[0]));
		detallesDeServicio = JSON.parse(window.localStorage.getItem('detallesDeServicio'));
		$('#numeroDelServicioSeleccionado').text(detallesDeServicio.id);
		$('#detallesDelServicioNombre').val(detallesDeServicio.nombre);
		$('#detallesDelServicioCedula').val(detallesDeServicio.cedula);
		$('#detallesDelServicioTelefono').val(detallesDeServicio.telefono);
		$('#detallesDelServicioLocalidad').val(detallesDeServicio.localidad);
		$('#detallesDelServicioLugar').val(detallesDeServicio.lugar);
		$('#detallesDelServicioTipo').val(detallesDeServicio.tipo);
		$('#detallesDelServicioNroControl').val(detallesDeServicio.control);
		$('#detallesDelServicioFecha').val(detallesDeServicio.fecha);
		$('#detallesDelServicioHoras').val(detallesDeServicio.horas);
		$('#detallesDelServicioObservacion').text(detallesDeServicio.observacion);
	}
}

class Visual
{
	static limpiar(campos, borrarCampos = 0)
	{
		if(Array.isArray(campos))
		{
			campos.forEach(v =>
			{
				$('#' + v + 'Error').text(' ');
				$('#' + v).css('borderColor', '#CED4DA');
			});

			if(borrarCampos == 1)
			{
				campos.forEach(v =>
				{
					$('#' + v).val('');
				});
			}
		}
		else
		{
			$('#' + campos + 'Error').text(' ');
			$('#' + campos).css('borderColor', '#CED4DA');

			if(borrarCampos == 1)
			{
				$('#' + campos).val('');
			}
		}

		return false;
	}

	static ensuciar(id, texto = 'Este campo es obligatorio.')
	{
		if(Array.isArray(id))
		{
			id.forEach(v =>
			{
				$('#' + v + 'Error').text(texto);
				$('#' + v).css('borderColor', 'red');
			});
		}
		else
		{
			$('#' + id + 'Error').text(texto);
			$('#' + id).css('borderColor', 'red');
		}

		return true;
	}

	static mostrarDivPrincipal(divId)
	{
		const Divs =
		[
			'tomarPedidoDiv',
			'coordinacionDeServiciosDiv',
			'reportesDiv'
		];

		$('.ocultarAuxiliar').css('display', 'none');
		Auxiliares.destruirTodasLasTablas();

		Divs.forEach(e =>
		{
			if(divId === e)
			{
				$('#' + e).show();
				$('.'+ e + 'MostrarAuxiliar').css('display', 'block');
			}
			else
			{
				$('#' + e).hide();
				$('.'+ e + 'MostrarAuxiliar').css('display', 'none');
			}
		});
	}

	static animacionMenu()
	{
		$('#logolateral').toggleClass('mostrarTexto');
		$('#menu').toggleClass('menuabierto');
		$('#menu').toggleClass('menu');
		$('.texto-btn1').toggle();
	}

	static mostrarModal(titulo, texto, html = false, codigoHTML = '')
	{
		$('#modalGenericoTexto').css('display', 'block');
		$('#modalGenericoHTML').css('display', 'none');
		$('#modalGenericoTitulo').text(titulo);
		$('#modalGenericoTexto').text(texto);
		if(html)
		{
			$('#modalGenericoHTML').html(codigoHTML);
			$('#modalGenericoHTML').css('display', 'block');
			$('#modalGenericoTexto').css('display', 'none');
		}

		$('#modalGenerico').css('display', 'block');
	}

	static coordinacionDeServiciosModificarDatosFinal(id, cedula, nombre, observacion, fecha, hora, horaPorDia, telefono, lugar, piso, sala, cama, tipo)
	{
		Auxiliares.destruirTodasLasTablas();

		Visual.limpiar
		([
			'coordinacionDeServiciosModificarDatosFinalTelefono',
			'coordinacionDeServiciosModificarDatosFinalLugarInternacion',
			'coordinacionDeServiciosModificarDatosFinalTipoDeServicio',
			'coordinacionDeServiciosModificarDatosFinalObservaciones',
			'coordinacionDeServiciosModificarDatosFinalSala',
			'coordinacionDeServiciosModificarDatosFinalCama',
			'coordinacionDeServiciosModificarDatosFinalPiso',
			'coordinacionDeServiciosModificarDatosFinalHorasServicio',
			'coordinacionDeServiciosModificarDatosFinalFechaInicio',
			'coordinacionDeServiciosModificarDatosFinalHoraInicio'
		]);

		$('#coordinacionDeServiciosModificarDatosFinalTipoDeServicio').off();
		$('#coordinacionDeServiciosModificarDatosFinalEnviar').off();

		$('#coordinacionDeServiciosModificarDatosFinalTipoDeServicio').change(e =>
		{
			e.preventDefault();
			Auxiliares.llenarHorarios(1, (parseInt($('#coordinacionDeServiciosModificarDatosFinalTipoDeServicio').val()) + 1), 'coordinacionDeServiciosModificarDatosFinalHorasServicio', false, false);
		});

		$('#coordinacionDeServiciosModificarDatosFinalEnviar').click(el =>
		{
			if(Control.coordinacionDeServiciosModificarDatosFinal(fecha, hora, horaPorDia, telefono, lugar, piso, sala, cama, tipo))
				AJAX.coordinacionDeServiciosModificarDatosFinal();
		});

		AJAX.serviciosDelCliente(cedula, 'coordinacionDeServiciosModificarDatosFinalTipoDeServicio', tipo);
		setTimeout(() =>
		{
			Auxiliares.llenarHorarios(6, 24, 'coordinacionDeServiciosModificarDatosFinalHoraInicio');
			AJAX.llenarLugaresDeInternacion('coordinacionDeServiciosModificarDatosFinalLugarInternacion', lugar);
			Auxiliares.llenarHorarios(1, (parseInt($('#coordinacionDeServiciosModificarDatosFinalTipoDeServicio').val()) + 1), 'coordinacionDeServiciosModificarDatosFinalHorasServicio', false, false);

			$('#coordinacionDeServiciosModificarDatosFinalID').val(id);
			$('#coordinacionDeServiciosModificarDatosFinalNombre').val(nombre);
			$('#coordinacionDeServiciosModificarDatosFinalCedula').val(cedula);
			$('#coordinacionDeServiciosModificarDatosFinalTelefono').val(telefono);
			$('#coordinacionDeServiciosModificarDatosFinalObservaciones').val(observacion),
			$('#coordinacionDeServiciosModificarDatosFinalSala').val(sala);
			$('#coordinacionDeServiciosModificarDatosFinalCama').val(cama);
			$('#coordinacionDeServiciosModificarDatosFinalPiso').val(piso);
			$('#coordinacionDeServiciosModificarDatosFinalHorasServicio').val(horaPorDia);
			$('#coordinacionDeServiciosModificarDatosFinalFechaInicio').val(fecha);
			$('#coordinacionDeServiciosModificarDatosFinalHoraInicio').val(hora);

			$('#coordinacionDeServiciosModificarDatosFinal').css('display', 'block');
		}, 300);
	}
}

class Control extends Visual
{
	static logIn()
	{
		let control = Visual.limpiar(['usuarioLogIn', 'passwordLogIn']);

		if($('#usuarioLogIn').val().length === 0)
			control = Visual.ensuciar('usuarioLogIn');

		if($('#passwordLogIn').val().length === 0)
			control = Visual.ensuciar('passwordLogIn');

		return !control;
	}

	static tomarPedido()
	{
		let control = true;

		$('#tomarPedidoTelefono').val($('#tomarPedidoTelefono').val().trim().replace('  ', ' ').replace('  ', ' '));

		if($('#tomarPedidoTelefono').val().length === 9)
		{
			if(this.celular('tomarPedidoTelefono'))
				control = false;
		}
		else if($('#tomarPedidoTelefono').val().length === 8)
		{
			if(this.telefono('tomarPedidoTelefono'))
				control = false;
		}
		else if($('#tomarPedidoTelefono').val().length < 9)
		{
			if(this.telefono('tomarPedidoTelefono'))
				control = false;
		}
		else if(this.corroborarValorDefault('tomarPedidoTelefono', ''))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoLugarInternacion', 0))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoTipoDeServicio', 0))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoSala', ''))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoCama', ''))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoPiso', ''))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoHorasServicio', 0))
			control = false;

		if(this.fecha('tomarPedidoFechaInicio'))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoHoraInicio', 0))
			control = false;

		return control;
	}

	static tomarPedidoNoSocio()
	{
		let control = true;

		if(this.corroborarValorDefault('tomarPedidoNombreNoSocio', ''))
			control = false;

		$('#tomarPedidoTelefonoNoSocio').val($('#tomarPedidoTelefonoNoSocio').val().trim().replace('  ', ' ').replace('  ', ' '));

		if($('#tomarPedidoTelefonoNoSocio').val().length === 9)
		{
			if(this.celular('tomarPedidoTelefonoNoSocio'))
				control = false;
		}
		else if($('#tomarPedidoTelefonoNoSocio').val().length === 8)
		{
			if(this.telefono('tomarPedidoTelefonoNoSocio'))
				control = false;
		}
		else if($('#tomarPedidoTelefonoNoSocio').val().length < 9)
		{
			if(this.telefono('tomarPedidoTelefonoNoSocio'))
				control = false;
		}
		else if(this.corroborarValorDefault('tomarPedidoTelefonoNoSocio', ''))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoLugarInternacionNoSocio', 0))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoTipoDeServicioNoSocio', 0))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoSalaNoSocio', ''))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoCamaNoSocio', ''))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoPisoNoSocio', ''))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoHorasServicioNoSocio', 0))
			control = false;

		if(this.fecha('tomarPedidoFechaInicioNoSocio'))
			control = false;

		if(this.corroborarValorDefault('tomarPedidoHoraInicioNoSocio', 0))
			control = false;

		return control;
	}

	static coordinacionDeServiciosModificarDatosFinal(fecha, hora, horaPorDia, telefono, lugar, piso, sala, cama, tipo)
	{
		let control = true;

		if
		(
			$('#coordinacionDeServiciosModificarDatosFinalFechaInicio').val()					=== fecha		&&
			parseInt($('#coordinacionDeServiciosModificarDatosFinalHoraInicio').val())			=== hora		&&
			parseInt($('#coordinacionDeServiciosModificarDatosFinalHorasServicio').val())		=== horaPorDia	&&
			$('#coordinacionDeServiciosModificarDatosFinalTelefono').val()						=== telefono	&&
			$('#coordinacionDeServiciosModificarDatosFinalLugarInternacion :selected').text()	=== lugar		&&
			parseInt($('#coordinacionDeServiciosModificarDatosFinalPiso').val())				=== piso		&&
			$('#coordinacionDeServiciosModificarDatosFinalSala').val()							=== sala		&&
			$('#coordinacionDeServiciosModificarDatosFinalCama').val()							=== cama		&&
			$('#coordinacionDeServiciosModificarDatosFinalTipoDeServicio :selected').text()		=== tipo
		)
		{
			alert('No se ha modificado dato alguno.');
			return false;
		}

		$('#coordinacionDeServiciosModificarDatosFinalTelefono').val($('#coordinacionDeServiciosModificarDatosFinalTelefono').val().trim().replace('  ', ' ').replace('  ', ' '));

		if($('#coordinacionDeServiciosModificarDatosFinalTelefono').val().length === 9)
		{
			if(this.celular('coordinacionDeServiciosModificarDatosFinalTelefono'))
				control = false;
		}
		else if($('#coordinacionDeServiciosModificarDatosFinalTelefono').val().length === 8)
		{
			if(this.telefono('coordinacionDeServiciosModificarDatosFinalTelefono'))
				control = false;
		}
		else if($('#coordinacionDeServiciosModificarDatosFinalTelefono').val().length < 9)
		{
			if(this.telefono('coordinacionDeServiciosModificarDatosFinalTelefono'))
				control = false;
		}
		else if(this.corroborarValorDefault('coordinacionDeServiciosModificarDatosFinalTelefono', ''))
			control = false;

		if(this.corroborarValorDefault('coordinacionDeServiciosModificarDatosFinalLugarInternacion', 0))
			control = false;

		if(this.corroborarValorDefault('coordinacionDeServiciosModificarDatosFinalTipoDeServicio', 0))
			control = false;

		if(this.corroborarValorDefault('coordinacionDeServiciosModificarDatosFinalSala', ''))
			control = false;

		if(this.corroborarValorDefault('coordinacionDeServiciosModificarDatosFinalCama', ''))
			control = false;

		if(this.corroborarValorDefault('coordinacionDeServiciosModificarDatosFinalPiso', ''))
			control = false;

		if(this.corroborarValorDefault('coordinacionDeServiciosModificarDatosFinalHorasServicio', 0))
			control = false;

		if(this.fecha('coordinacionDeServiciosModificarDatosFinalFechaInicio'))
			control = false;

		if(this.corroborarValorDefault('coordinacionDeServiciosModificarDatosFinalHoraInicio', 0))
			control = false;

		return control;
	}

	static guardarCoordinacionDeServiciosAsignarAcompanhante()
	{
		let control = true;

		if(this.fecha('coordinacionDeServiciosAsignarAcompanhanteFecha'))
			control = false;

		if(this.corroborarValorDefault('coordinacionDeServiciosAsignarAcompanhanteTurnos', 0))
			control = false;
		else
		{
			let desdeT1, hastaT1, diferenciaT1, desdeT2, hastaT2, diferenciaT2, desdeT3, hastaT3, diferenciaT3;
			let horarios = Array;

			Visual.limpiar([
				'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno1',
				'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno2',
				'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno3', 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno3']);

			if(parseInt($('#coordinacionDeServiciosAsignarAcompanhanteTurnos').val()) < 1 || parseInt($('#coordinacionDeServiciosAsignarAcompanhanteTurnos').val()) > 3)
			{
				if(this.corroborarValorDefault('coordinacionDeServiciosAsignarAcompanhanteTurnos', parseInt($('#coordinacionDeServiciosAsignarAcompanhanteTurnos').val()), 'Valor incorrecto.'))
				control = false;

				alert("Se ha detectado una modificación del archivo.\nSe recargará la página para prevenir fallos.");
				location.reload(true);
			}
			else
			{
				for(let i = 1; i <= parseInt($('#coordinacionDeServiciosAsignarAcompanhanteTurnos').val()); i++)
				{
					window['desdeT' + i] = parseInt($('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno' + i).val());
					window['hastaT' + i] = (parseInt($('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno' + i).val()) < 800 && parseInt($('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno' + i).val()) > 1599)
						? (parseInt($('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno' + i).val()) + 2400)
						: parseInt($('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno' + i).val());
					window['diferenciaT' + i] = (window['hastaT' + i] - window['desdeT' + i])/100;

					horarios[i] =
					{
						desde: window['desdeT' + i],
						hasta: window['hastaT' + i],
						idDesde: 'coordinacionDeServiciosAsignarAcompanhanteDesdeTurno' + i,
						idHasta: 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno' + i
					};

					if(this.nombreCompleto('coordinacionDeServiciosAsignarAcompanhanteNombreTurno' + i))
						control = false;
					if(this.cedula('coordinacionDeServiciosAsignarAcompanhanteCedulaTurno' + i))
						control = false;
					if(isNaN(parseInt($('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno' + i + ' :selected').text())) || isNaN(parseInt($('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno' + i + ' :selected').text())))
					{
						if(isNaN(parseInt($('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno' + i + ' :selected').text())))
							control = !Visual.ensuciar('coordinacionDeServiciosAsignarAcompanhanteDesdeTurno' + i, 'Seleccione una hora.');
						if(isNaN(parseInt($('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno' + i + ' :selected').text())))
							control = !Visual.ensuciar('coordinacionDeServiciosAsignarAcompanhanteHastaTurno' + i, 'Seleccione una hora.');
					}
					else if(window['desdeT' + i] > window['hastaT' + i])
						control = !Visual.ensuciar(['coordinacionDeServiciosAsignarAcompanhanteDesdeTurno' + i, 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno' + i], 'Revise las horas');
					else if(window['diferenciaT' + i] !== 8)
					{
						if(window['diferenciaT' + i] > 8)
						{
							if(!confirm("La diferencia del turno " + i + " horas es mayor a 8\n¿Está bien así?"))
								control = !Visual.ensuciar(['coordinacionDeServiciosAsignarAcompanhanteDesdeTurno' + i, 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno' + i], 'Revise la diferencia de horas.');
						}
						else
						{
							if(!confirm("La diferencia del turno " + i + " horas es menor a 8\n¿Está bien así?"))
								control = !Visual.ensuciar(['coordinacionDeServiciosAsignarAcompanhanteDesdeTurno' + i, 'coordinacionDeServiciosAsignarAcompanhanteHastaTurno' + i], 'Revise la diferencia de horas.');
						}
					}
				}
				if(parseInt($('#coordinacionDeServiciosAsignarAcompanhanteTurnos').val()) > 1)
				{
					$.each(horarios, (i, v) =>
					{
						$.each(horarios, (i2, v2) =>
						{
							if(i === i2)
								return;
 
							if(v.desde > 2399)
								v.desde = v.desde - 2400;
							if(v2.desde > 2399)
								v2.desde = v2.desde - 2400;
							if(v.hasta > 2399)
								v.hasta = v.hasta - 2400;
							if(v2.hasta > 2399)
								v2.hasta = v2.hasta - 2400;

							if((v.desde > v2.desde && v.desde < v2.hasta) || (v.hasta > v2.desde && v.hasta < v2.hasta))
								control = !Visual.ensuciar([v.idDesde, v.idHasta, v2.idDesde, v2.idHasta], 'Horas superpuestas.');
						});
					});
				}
			}
		}

		return false;
	}

	static buscarCedula(parametro)
	{
		let control =  true;

		if(this.cedula(parametro + 'BuscarCedula'))
			control = false;

		return control;
	}

	static nombreCompleto(id)
	{
		$('#' + id).val($('#' + id).val().trim());
		$('#' + id).val($('#' + id).val().replace(/(\ \ )+/g, ' '));

		if($('#' + id).val().length === 0)
			return super.ensuciar(id);
		else if($('#' + id).val().split(' ').length < 2)
			return super.ensuciar(id, 'Por favor ingrese el nombre completo.');

		return super.limpiar(id);
	}

	static fecha(id)
	{
		if(/[\d]{2}\/[\d]{2}\/[\d]{2}$/.test($('#' + id).val()))
		{
			let nuevoValor = $('#' + id).val().split('/');
			$('#' + id).val(nuevoValor[0] + '/' + nuevoValor[1] + '/20' + nuevoValor[2])
		}

		if($('#' + id).val().length === 0)
			return super.ensuciar(id);
		else if(!/^[\d]{2}\/[\d]{2}\/[\d]{4}$/.test($('#' + id).val()))
			return super.ensuciar(id, 'Fecha mal formada.');

		return super.limpiar(id);
	}

	static telefono(id)
	{
		if($('#' + id).val().length === 0)
			return super.ensuciar(id);
		else if($('#' + id).val().length !== 8)
			return super.ensuciar(id, 'El teléfono ingresado es inválido');
		else if(!/^([0-9])*$/.test($('#' + id).val()))
			return super.ensuciar(id, 'El teléfono sólo debe contener números');
		else if(!/^(2|4)/.test($('#' + id).val()))
			return super.ensuciar(id, 'El teléfono está mal formado');

		return super.limpiar(id);
	}

	static celular(id)
	{
		if ($('#' + id).val().length === 0)
			return super.ensuciar(id);
		else if($('#' + id).val().length !== 9)
			return super.ensuciar(id, 'El celular ingresado es inválido');
		else if(!/^([0-9])*$/.test($('#' + id).val()))
			return super.ensuciar(id, 'El celular sólo debe contener números');
		else if(!/^(09)\d{7}/.test($('#' + id).val()))
			return super.ensuciar(id, 'El celular está mal formado');

		return super.limpiar(id);
	}

	static cedula(id)
	{
		if($('#' + id).val().length === 0)
			return super.ensuciar(id);

		let cedula 		= $('#' + id).val();
		let arrCoefs	= [2, 9, 8, 7, 6, 3, 4, 1];
		let suma		= 0;
		let difCoef		= parseInt(arrCoefs.length - cedula.length);
		for(let i = cedula.length - 1; i > -1; i--)
		{
			let dig 	= cedula.substring(i, i + 1);
			let digInt 	= parseInt(dig);
			let coef 	= arrCoefs[i + difCoef];
			suma = suma + digInt * coef;
		}

		if(((suma % 10) == 0))
			return super.limpiar(id);
		else
			return super.ensuciar(id, 'La cédula ingresada no es correcta.');
	}

	static email(id)
	{
		if ($('#' + id).val().length === 0)
			return super.ensuciar(id);
		else if(!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($('#' + id).val()))
			return super.ensuciar(id, 'El email ingresado no es válido.');

		return super.limpiar(id);
	}

	static corroborarValorDefault(id, valorDefault, textoError = '')
	{
		if(textoError === '')
			textoError = 'Por favor seleccione un valor.';

		if($('#' + id).val() == valorDefault)
			return super.ensuciar(id, textoError);

		return super.limpiar(id);
	}

	static posiblesReintegros()
	{
		if($('#tomarPedidoTipoDeServicio :selected').text().substring(0, 9) === 'Reintegro')
			return confirm("Ha seleccionado un reintegro.\nSi presiona \"aceptar\" se abrirá un nuevo formulario.");
	}
}

class Auxiliares
{
	static llenarHorarios(desde, hasta, selectId, intervalosDeMediaHora = true, minutos = true)
	{
		$('#' + selectId).empty();
		let horariosDisponibles = '<option value="0">Seleccione un horario</option>';

		for(let i = desde; i < hasta; i++)
			horariosDisponibles += (intervalosDeMediaHora)
			?
				`
					<option value="` + i + `00">` + i + `:00</option>
					<option value="` + i + `30">` + i + `:30</option>
				`
			:
			(
				(minutos)
				? '<option value="' + i + '00">' + i + ':00</option>'
				: '<option value="' + i + '">' + i + '</option>'
			);

		$(horariosDisponibles).appendTo('#' + selectId);
	}

	static destruirTodasLasTablas()
	{
		$('.ocultarAuxiliar').hide();
		$.fn.dataTable.fnTables().forEach(e => $(e).DataTable().destroy());
	}

	static cambiarTituloActual(titulo = tituloDeSector)
	{
		$('#sectorActual').text(titulo);
		document.title = titulo;
	}

	static irDirectamenteAlServicio(id, idinfo)
	{
		AJAX.mostrarCoordinacionDeServiciosAsignarAcompanhante(idinfo);
		$('#coordinacionDeServiciosAsignarAcompanhanteReferencia').val(idinfo);
		$('#numeroDelServicioSeleccionado').text(idinfo);
		AJAX.insertarCoordinacionDeServiciosAsignarAcompanhante(id);
	}

	static eliminarTurnoParticular(turno)
	{
		$('#coordinacionDeServiciosAsignarAcompanhanteTurnos :eq(' + parseInt($('#coordinacionDeServiciosAsignarAcompanhanteTurnos').val() - 1) + ')').prop('selected', true);
		$('#coordinacionDeServiciosAsignarAcompanhanteTurnos').change();

		const valoresT2 =
		{
			nombre:	$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno2').val(),
			cedula:	$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno2').val(),
			desde:	$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2').val(),
			hasta:	$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno2').val()
		};
		const valoresT3 =
		{
			nombre:	$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno3').val(),
			cedula:	$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno3').val(),
			desde:	$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno3').val(),
			hasta:	$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno3').val()
		};

		switch (parseInt(turno))
		{
			case 1:
				$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno1').val(valoresT2.nombre);
				$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno1').val(valoresT2.cedula);
				$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno1').val(valoresT2.desde);
				$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno1').val(valoresT2.hasta);
				
				$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno2').val(valoresT3.nombre);
				$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno2').val(valoresT3.cedula);
				$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2').val(valoresT3.desde);
				$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno2').val(valoresT3.hasta);
				break;
			case 2:
				$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno2').val(valoresT3.nombre);
				$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno2').val(valoresT3.cedula);
				$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno2').val(valoresT3.desde);
				$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno2').val(valoresT3.hasta);
				break;
		}

		for(let i = parseInt($('#coordinacionDeServiciosAsignarAcompanhanteTurnos :selected').val()) + 1; i <= 3; i++)
		{
			$('#coordinacionDeServiciosAsignarAcompanhanteNombreTurno'	+ i).val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteCedulaTurno'	+ i).val('');
			$('#coordinacionDeServiciosAsignarAcompanhanteDesdeTurno'	+ i + ' :first-child').prop('selected', true);
			$('#coordinacionDeServiciosAsignarAcompanhanteHastaTurno'	+ i + ' :first-child').prop('selected', true);
		}
	}

	static formatoHTMLDeInfoServicio(e)
	{
		e.activo		= (e.activo === 1)
		? '<label style="color: green">Activo:</label> <span style="color: green">Sí</span> </br>'
		: '<label style="color: red">Activo:</label> <span style="color: red">No</span> </br>';
		e.pendiente		= (e.pendiente === 1)
		? '<label style="color: green">Pendiente:</label> <span style="color: green">Sí</span> </br>'
		: '<label style="color: red">Pendiente:</label> <span style="color: red">No</span> </br>';
		e.finalizado	= (e.finalizado === 1)
		? '<label style="color: green">Finalizado:</label> <span style="color: green">Sí</span> </br>'
		: '<label style="color: red">Finalizado:</label> <span style="color: red">No</span> </br>';
		e.cancelado		= (e.cancelado === 1)
		? '<label style="color: green">Cancelado:</label> <span style="color: green">Sí</span> </br>'
		: '<label style="color: red">Cancelado:</label> <span style="color: red">No</span> </br>';


		let codigoHTML =
		`
		<label>Nombre:</label> <span>`		+ e.nombre		+ `</span> </br>
		<label>Cédula:</label> <span>`		+ e.cedula		+ `</span> </br>
		<label>Teléfono:</label> <span>`	+ e.telefono	+ `</span> </br>
		<label>Localidad:</label> <span>`	+ e.localidad	+ `</span> </br>
		<label>Lugar:</label> <span>`		+ e.lugar		+ `</span> </br>
		<label>Tipo:</label> <span>`		+ e.tipo		+ `</span> </br>
		<label>Nro control:</label> <span>`	+ e.control		+ `</span> </br>
		<label>Fecha:</label> <span>`		+ e.fecha		+ `</span> </br>
		<label>Horas:</label> <span>`		+ e.horas		+ `</span> </br>
		` /* + e.activo + ' ' + e.pendiente + ' ' + e.finalizado + ' ' + e.cancelado */ + `
		<label>Observaciones:</label> <textarea readonly>` + e.observacion + `</textarea> </br>
		`;
		return codigoHTML;
	}
}

//VARIABLES GLOBALES

var configuracionDataTable =
{
	searching:			true,
	paging:				true,
	lengthChange:		false,
	ordering:			true,
	info:				true,
	order:
	[
		[1, 'DESC']
	],
	language:
	{
		zeroRecords:	'No se encontraron registros.',
		info:			'Pagina _PAGE_ de _PAGES_',
		infoEmpty:		'No hay registros disponibles',
		infoFiltered:	'(filtrado de _MAX_ hasta records)',
		search:			'Buscar:',
		paginate:
		{
			first:		'Primero',
			last:		'Último',
			next:		'Siguiente',
			previous:	'Anterior'
		},
	}
}

var configuracionDatePicker =
{
	minDate:			0,
	isRTL:				false,
	showMonthAfterYear:	false,
	changeMonth:		true,
	changeYear:			true,
	showOtherMonths:	true,
	selectOtherMonths:	true,
	closeText:			'Cerrar',
	prevText:			'&#x3C;Ant',
	nextText:			'Sig&#x3E;',
	currentText:		'Hoy',
	weekHeader:			'Sm',
	dateFormat:			'dd/mm/yy',
	regional:			'es',
	monthNames:
	[
		'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
	],
	monthNamesShort:
	[
		'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'
	],
	dayNames:
	[
		'domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'
	],
	dayNamesShort:
	[
		'dom', 'lun', 'mar', 'mié', 'jue', 'vie', 'sáb'
	],
	dayNamesMin:
	[
		'D', 'L', 'M', 'X', 'J', 'V', 'S'
	]
}

var tituloDeSector = 'Toma pedido';

var detallesDeServicio = Object;