/**
 * ExcepcionesBundle/Resources/public/adminApp/services/excepcionesHomeSvc.js
 */
angular.module('app')
    .service('excepcionesHomeSvc', ['$http',
            function ($http) {
                var message = '';

                function setMessage(newMessage) {
                    //console.log(newMessage)
                    message = newMessage;
                }

                function getMessage() {
                    return message;
                }


                //controller deleted
                function getExceptions() {
                    return $http.get(Routing.generate('exceptions_bundles_routes',{},true));
                }

                return {
                    setMessage: setMessage,
                    getMessage: getMessage,
                    getExceptions: getExceptions,
                    $get: function () {

                    }
                }
            }
        ]
    );