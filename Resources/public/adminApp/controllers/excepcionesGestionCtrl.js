/**
 * ExcepcionesBundle/Resources/public/adminApp/controllers/excepcionesGestionCtrl.js
 */
angular.module('app')
    .controller('excepcionesGestionCtrl',
        ['$scope', 'excepcionesGestionSvc', 'toastr', '$mdDialog',
            function ($scope, excepcionesGestionSvc, toastr, $mdDialog) {

                excepcionesGestionSvc.getCSRFtoken()
                    .success(function (response) {
                        $scope.token = response;
                    })
                    .error(function (response) {
                    });

                $scope.alphanumeric = '[a-zA-Z0-9 ]+';
                $scope.sololetras = '[a-z A-Z]+';
                $scope.numeric = '[0-9]+';
                $scope.alphanumericMess = "Solo se permiten letras y números.";
                $scope.sololetrasMess = "Solo se permiten letras.";
                $scope.numericMess= "Solo se permiten números.";


                var tabs = [];
                var tabcount = 0;
                var idiomasUsados = [];
                $scope.modificable = false;
                $scope.showprod = false;

                $scope.wasmodified = false;

                $scope.modif = function () {
                    $scope.wasmodified = true;
                };

                $scope.idiomas = [
                    {label: 'es'},
                    {label: 'en'},
                    {label: 'fr'},
                    {label: 'ru'}
                ];

                $scope.tabcount = tabcount;

                excepcionesGestionSvc.getBundlesWithExceptions()
                    .success(function (response) {
                        $scope.info = response;
                    })
                    .error(function (response) {
                        alert(response);
                    });


                $scope.eraseException = function (ev) {
                    var confirm = $mdDialog.confirm()
                        .title('Confirmación de cambios')
                        .textContent('¿Está seguro que desea eliminar la excepción?')
                        .targetEvent(ev)
                        .ok('Si')
                        .cancel('No');
                    $mdDialog.show(confirm).then(function () {
                        //si se selecciona que si:
                        excepcionesGestionSvc.deleteException($scope.bundle, $scope.excepcion)
                            .success(function (response) {
                                toastr.success(response);
                                //borrando elementos sin recargar pagina
                                $scope.codigo = null;
                                $scope.bundle = null;
                                $scope.excepcion = null;
                                $scope.showprod = null;
                                tabs = [];
                                idiomasUsados = [];
                                $scope.tabs = null;
                                tabcount = 0;
                                $scope.tabcount = 0;
                                $scope.tMensaje = null;
                                $scope.tDescrip = null;
                                $scope.tIdioma = null;
                                $scope.wasmodified = false;
                                excepcionesGestionSvc.getBundlesWithExceptions()
                                    .success(function (response) {
                                        $scope.info = response;
                                    })
                                    .error(function (response) {
                                        alert(response);
                                    });

                            })
                            .error(function (response) {
                                toastr.error(response);
                            });
                    }, function () {
                        //en caso contrario:
                        //toastr.info("Se ha cancelado la operación.");
                    });
                };

                $scope.loadTabs = function () {
                    $scope.tabs = [];
                    $scope.modificable = true;
                    $scope.showprod = false;
                    excepcionesGestionSvc.getTraslationsByBundleAndCode($scope.bundle, $scope.excepcion)
                        .success(function (response) {
                            tabs = response;
                            $scope.tabs = tabs;
                            //cargando los idiomas en idiomas ya usados
                            for (trans in response) {
                                idiomasUsados.push(response[trans]['idioma']);
                                //console.log(response[trans]['idioma']);
                            }

                            $scope.codigo = $scope.excepcion;
                            tabcount = $scope.tabs.length;
                            $scope.tabcount = tabcount;
                            excepcionesGestionSvc.IsShowProd($scope.bundle, $scope.excepcion)
                                .success(function (response) {
                                    if (response == "true") {
                                        $scope.showprod = true;
                                    }
                                })
                                .error(function (response) {
                                    toastr.error(response);
                                });
                        })
                        .error(function (response) {
                            toastr.error(response);
                        });
                };

                $scope.loadException = function () {
                    $scope.exceptionsbundle = null;
                    excepcionesGestionSvc.getExceptions($scope.bundle)
                        .success(function (response) {
                            $scope.exceptionsbundle = response;
                        })
                        .error(function (response) {
                            toastr.error(response);
                        });

                };

                $scope.addTab = function (idioma, mensaje, descripcion) {
                    var index = idiomasUsados.indexOf(idioma);
                    if (index >= 0) {
                        toastr.error("Ya existe una traducción en el idioma especificado.");
                        return;
                    }
                    $scope.wasmodified = true;
                    idiomasUsados.push(idioma);
                    tabs.push({idioma: idioma, mensaje: mensaje, descrip: descripcion});
                    $scope.tabs = tabs;
                    tabcount = tabcount + 1;
                    $scope.tabcount = tabcount;

                    $scope.tIdioma = null;
                    $scope.tMensaje = null;
                    $scope.tDescrip = null;
                };

                $scope.removeTab = function (tab) {
                    var index = tabs.indexOf(tab);
                    tabs.splice(index, 1);
                    idiomasUsados.splice(index, 1);
                    tabcount = tabcount - 1;
                    $scope.tabcount = tabcount;
                    $scope.wasmodified = true;
                };

                $scope.guardarCambios = function (ev) {
                    var confirm = $mdDialog.confirm()
                        .title('Confirmación de cambios')
                        .textContent('¿Está seguro que desea guardar los cambios?')
                        .targetEvent(ev)
                        .ok('Si')
                        .cancel('No');
                    $mdDialog.show(confirm).then(function () {
                        //si se selecciona que si:
                        var data = {
                            uci_boson_excepcionesbundle_data: {
                                codigo: $scope.codigo,
                                codigoAnterior: $scope.excepcion,
                                bundle: $scope.bundle,
                                listTranslation: tabs,
                                showprod: $scope.showprod,
                                _token: $scope.token
                            }
                        };
                        excepcionesGestionSvc.ModifyException(data)
                            .success(function (response) {
                                toastr.success(response);
                                //borrando elementos sin recargar pagina
                                $scope.codigo = null;
                                $scope.bundle = null;
                                $scope.excepcion = null;
                                $scope.showprod = null;
                                tabs = [];
                                idiomasUsados = [];
                                $scope.tabs = null;
                                tabcount = 0;
                                $scope.tabcount = 0;
                                $scope.tMensaje = null;
                                $scope.tDescrip = null;
                                $scope.tIdioma = null;
                                $scope.wasmodified = false;
                                excepcionesGestionSvc.getBundlesWithExceptions()
                                    .success(function (response) {
                                        $scope.info = response;
                                    })
                                    .error(function (response) {
                                        alert(response);
                                    });
                            })
                            .error(function (response) {
                                console.log(response);
                                toastr.error(response);
                            });
                    }, function () {
                        //en caso contrario:
                        //toastr.info("Se ha cancelado la operación.");
                    });
                };

            }
        ]
    );