/**
 * ExcepcionesBundle/Resources/public/adminApp/controllers/excepcionesAddCtrl.js
 */
angular.module('app')
    .controller('excepcionesAddCtrl',
        ['$scope', 'excepcionesAddSvc', 'toastr', '$mdDialog',
            function ($scope, excepcionesAddSvc, toastr, $mdDialog) {

                $scope.alphanumeric = '[a-zA-Z0-9]+';
                $scope.sololetras = '[a-z A-Z]+';
                $scope.numeric = '[0-9]+';
                $scope.alphanumericMess = "Solo se permiten letras y números.";
                $scope.sololetrasMess = "Solo se permiten letras.";
                $scope.numericMess= "Solo se permiten números.";


                var tabcount = 0;
                var tabs = [];
                var idiomasUsados = [];

                var idiomas = [
                    {label: 'es'},
                    {label: 'en'},
                    {label: 'fr'},
                    {label: 'ru'}
                ];

                $scope.tabs = tabs;
                $scope.idiomas = idiomas;
                $scope.tabcount = tabcount;

                excepcionesAddSvc.showCurrentInfo()
                    .success(function (response) {
                        $scope.info = response;
                    })
                    .error(function (response) {
                        alert(response);
                    });

                $scope.AdicionarClick = function (ev) {

                    var confirm = $mdDialog.confirm()
                        .title('Confirmación de cambios')
                        .textContent('¿Está seguro que desea adicionar una nueva excepción?')
                        .targetEvent(ev)
                        .ok('Si')
                        .cancel('No');
                    $mdDialog.show(confirm).then(function () {
                        //si se selecciona que si:
                        var data = {
                            uci_boson_excepcionesbundle_data: {
                                codigo: $scope.codigo,
                                codigoAnterior: null,
                                bundle: $scope.bundle,
                                listTranslation: tabs,
                                showprod: $scope.showprod
                            }
                        };

                        excepcionesAddSvc.InsertException(data)
                            .success(function (response) {
                                toastr.success("La excepción ha sido modificada satisfactoriamente");
                                //location.reload();
                                $scope.codigo = null;
                                $scope.bundle = null;
                                $scope.showprod = null;
                                $scope.tabs = null;
                                tabs = [];
                                idiomasUsados = [];
                                tabcount = 0;
                                $scope.tabcount = 0;
                                $scope.tMensaje = null;
                                $scope.tDescrip = null;
                                $scope.tIdioma = null;

                            })
                            .error(function (response) {
                                toastr.error(response);
                            });
                    }, function () {
                        //en caso contrario:
                        toastr.info("Se ha cancelado la operación");
                    });


                };

                $scope.addTab = function (idioma, mensaje, descripcion) {
                    var index = idiomasUsados.indexOf(idioma);
                    if (index >= 0) {
                        toastr.error("Ya existe una traducción en el idioma especificado");
                        return;
                    }
                    idiomasUsados.push(idioma);
                    tabs.push({idioma: idioma, mensaje: mensaje, descrip: descripcion});
                    $scope.tabs = tabs;
                    tabcount = tabcount + 1;
                    $scope.tabcount = tabcount;
                };
                $scope.removeTab = function (tab) {
                    var index = tabs.indexOf(tab);
                    tabs.splice(index, 1);
                    idiomasUsados.splice(index, 1);
                    tabcount = tabcount - 1;
                    $scope.tabcount = tabcount;
                };
            }
        ]
    );