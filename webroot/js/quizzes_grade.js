/**
 * @fileoverview Questionnaire graph Javascript
 * @author info@allcreator.net (Allcreator co.)
 */

NetCommonsApp.requires.push('nvd3');


/**
 * Questionnaire Graph Javascript
 *
 * @param {string} Controller name
 * @param {function($scope)} Controller
 */
NetCommonsApp.controller('QuizGrade',
    function($scope, $window, $sce, $timeout, $log) {
      $scope.initialize = function(correctRate) {

        $scope.config = {
          chart: {
            'type': 'multiBarHorizontalChart',
            'height': 120,
            'showControls': false,
            'showValues': true,
            'duration': 500,
            x: function(d) {return '';},
            y: function(d) {return d.value;},
            'xAxis': {
              'showMaxMin': false
            },
            'yAxis': {
              'axisLabel': '%'
            },
            'stacked': true
          }
        };
        $scope.data = correctRate;
      };
    });
