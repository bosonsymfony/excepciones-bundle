/**
 * ExcepcionesBundle/Resources/public/adminApp/services/excepcionesGestionSvc.js
 */
angular.module('app')
    .service('excepcionesGestionSvc', ['$http',
            function ($http) {
                var message = '';

                function setMessage(newMessage) {
                    message = newMessage;
                }

                function getMessage() {
                    return message;
                }

                function getBundlesWithExceptions() {
                    return $http.get(Routing.generate('excepciones_get_bundleswithexceptions', {}, true));
                }

                function getExceptions(bundle) {
                    return $http.get(Routing.generate('excepciones_get_exceptionbybundle', {bundle: bundle}, true));
                }

                function getTraslationsByBundleAndCode(bundle, codigoExcepcion) {
                    return $http.get(Routing.generate('get_exceptionTranslation_bundleCode', {
                        bundle: bundle,
                        codigoExcepcion: codigoExcepcion
                    }, true));
                }

                function ModifyException(data) {
                    return $http.post(Routing.generate('exception_modify_data', {}, true), data);
                }

                function IsShowProd(bundle, codigoExcepcion) {
                    return $http.get(Routing.generate('exception_isShowInProd', {
                        bundle: bundle,
                        codigoExcepcion: codigoExcepcion
                    }, true));
                }

                function deleteException(bundle, codigoExcepcion) {
                    return $http.get(Routing.generate('exception_erase_data', {
                        bundle: bundle,
                        codigoExcepcion: codigoExcepcion
                    }));
                }

                return {
                    setMessage: setMessage,
                    getMessage: getMessage,
                    deleteException: deleteException,
                    getBundlesWithExceptions: getBundlesWithExceptions,
                    getTraslationsByBundleAndCode: getTraslationsByBundleAndCode,
                    getExceptions: getExceptions,
                    IsShowProd: IsShowProd,
                    ModifyException: ModifyException,
                    $get: function () {

                    }
                }
            }
        ]
    );