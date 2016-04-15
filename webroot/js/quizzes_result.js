/**
 * @fileoverview Questionnaire graph Javascript
 * @author info@allcreator.net (Allcreator co.)
 */

NetCommonsApp.requires.push('nvd3');


/**
 * Questionnaire ResultGraph Javascript
 *
 * @param {string} Controller name
 * @param {function($scope)} Controller
 */
NetCommonsApp.controller('QuizResult',
    function($scope, $window, $sce, $timeout, $log) {
      $scope.initialize = function(scoreDistribution) {
        $scope.opt = {
          chart: {
            'type': 'multiBarChart',
            'height': 250,
            'showControls': false,
            'showValues': true,
            'duration': 500,
            'margin' : {top: 10, right: 0, bottom: 50, left: 100},
            x: function(d) {return d.label;},
            y: function(d) {return d.value;},
            'xAxis': {
              'axisLabel': '得点',
              'showMaxMin': false
            },
            'yAxis': {
              'axisLabel': '人数'
            }
          }
        };
        $scope.data = [
          {
            'key': '得点',
            'color': '#777'
          }
        ];
        $scope.data[0]['values'] = new Array();
        angular.forEach(scoreDistribution, function(obj) {
          $scope.data[0]['values'].push(obj);
        });
      };
    });
NetCommonsApp.controller('QuizResultView',
    function($scope, $window, $sce, $timeout, $log,
             NetCommonsBase, NetCommonsFlash) {
      $scope.initialize = function(scoreHistory) {
        $scope.opt = {
          chart: {
            'type': 'lineChart',
            'height': 250,
            'showControls': false,
            'showValues': true,
            'duration': 500,
            'margin' : {top: 100, right: 80, bottom: 50, left: 80},
            x: function(d) {return d.answerNumber;},
            y: function(d) {return d.summaryScore;},
            'xAxis': {
              'axisLabel': '回数',
              'showMaxMin': false
            },
            'yAxis': {
              'axisLabel': '得点'
            },
            'title': {
              'enable': true,
              'text': 'あなたのこれまでの成績履歴'
            }
          }
        };
        $scope.data = [
          {
            'key': 'あなたのこれまでの成績履歴',
            'color': '#777'
          }
        ];
        $scope.data[0]['values'] = new Array();
        angular.forEach(scoreHistory, function(obj) {
          $scope.data[0]['values'].push(obj);
        });
      };
    });
