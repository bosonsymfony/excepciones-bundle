/**
 * ExcepcionesBundle/Resources/public/adminApp/services/excepcionesAddSvc.js
 */
angular.module('app')
    .service('excepcionesAddSvc', ['$http',
            function ($http) {
                var message = '';

                function setMessage(newMessage) {
                    message = newMessage;
                }

                function getMessage() {
                    return message;
                }

                function showCurrentInfo() {
                    return $http.get(Routing.generate('excepciones_get_info',{},true));
                }

                function InsertException(data) {
                    return $http.post(Routing.generate('exception_insert_data',{},true),data);
                }


                return {
                    setMessage: setMessage,
                    getMessage: getMessage,
                    showCurrentInfo:showCurrentInfo,
                    InsertException: InsertException,
                    $get: function () {

                    }
                }
            }
        ]
    );