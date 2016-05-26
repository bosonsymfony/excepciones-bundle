
/**
 * ExcepcionesBundle/Resources/public/adminApp/filters/excepcionesHomeFilter.js
 */
angular.module('app')
        .filter('excepcionesHomeFilter',
                function () {
                    return function (input) {
                        input = input || '';
                        var out = "";
                        for (var i = 0; i < input.length; i++) {
                            out = input.charAt(i) + out;
                        }
                        return out;
                    }
                }
        );