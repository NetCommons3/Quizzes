/**
 * @fileoverview Quiz Javascript
 * @author info@allcreator.net (Allcreator Co.)
 */
/**
 * The following features are still outstanding: popup delay, animation as a
 * function, placement as a function, inside, support for more triggers than
 * just mouse enter/leave, html popovers, and selector delegatation.
 */
/**
 * Quiz Edit Javascript
 *
 * @param {string} Controller name
 * @param {function($scope, $sce)} Controller
 */

NetCommonsApp.controller('QuizzesEdit',
    function($scope, NetCommonsWysiwyg, $timeout) {

      /**
       * tinymce
       *
       * @type {object}
       */
      $scope.tinymce = NetCommonsWysiwyg.new();

      /**
       * Initialize
       *
       * @return {void}
       */
      $scope.initialize =
          function(frameId, isPublished, quiz) {
        $scope.frameId = frameId;
        $scope.isPublished = isPublished;
        $scope.quiz = quiz;
        $scope.quiz.quiz.estimatedTime =
            parseInt($scope.quiz.quiz.estimatedTime);
        $scope.quiz.quiz.passingGrade = parseInt($scope.quiz.quiz.passingGrade);
      };
    });
