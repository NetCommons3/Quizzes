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
 * Quiz QuestionEdit Javascript
 *
 * @param {string} Controller name
 * @param {function($scope, $sce)} Controller
 */

angular.module('numfmt-error-module', [])
.run(function($rootScope) {
      $rootScope.typeOf = function(value) {
        return typeof value;
      };
    })
.directive('stringToNumber', function() {
      return {
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {
          ngModel.$parsers.push(function(value) {
            return '' + value;
          });
          ngModel.$formatters.push(function(value) {
            return parseFloat(value, 10);
          });
        }
      };
    });
NetCommonsApp.requires.push('numfmt-error-module');


/**
 * html tag strip
 */
angular.module('html-to-plaintext-module', [])
.filter('htmlToPlaintext', function() {
      return function(text, limit) {
        return String(text).replace(/<[^>]+>/gm, '').slice(0, limit);
      }
    }
    );
NetCommonsApp.requires.push('html-to-plaintext-module');

NetCommonsApp.controller('QuizzesEditQuestion',
    ['$scope', 'NetCommonsWysiwyg', 'quizzesMessages', '$timeout',
      function($scope, NetCommonsWysiwyg, quizzesMessages, $timeout) {

       /**
        * tinymce
        *
        * @type {object}
        */
       $scope.tinymce = NetCommonsWysiwyg.new();

       /**
        * serverValidationClear method
        *
        * @param {number} users.id
        * @return {string}
        */
       $scope.isTrue = '1';

       /**
        * variables
        *
        * @type {Object.<string>}
        */
       var variables = {

         /**
          * Relative path to login form
          *
          * @const
          */
         USES_USE: '1',

         TYPE_SELECTION: '1',
         TYPE_MULTIPLE_SELECTION: '2',
         TYPE_WORD: '3',
         TYPE_TEXT_AREA: '4',
         TYPE_MULTIPLE_WORD: '5',

         ANSWER_DELIMITER: '#||||||#',
         DEFAULT_ITEM_COUNT: 3,
         MAX_QUESTION_COUNT: 50,
         MAX_CHOICE_COUNT: 20
       };

       /**
        * Initialize
        *
        * @return {void}
        */
       $scope.initialize = function(frameId, isPublished, quiz) {
         $scope.frameId = frameId;
         $scope.isPublished = isPublished;
         $scope.quiz = quiz;
         $scope.quiz.quizPage = $scope.toArray(quiz.quizPage);

         // 各ページ処理
         for (var pIdx = 0; pIdx < $scope.quiz.quizPage.length; pIdx++) {
           var page = $scope.quiz.quizPage[pIdx];

           $scope.quiz.quizPage[pIdx].tabActive = false;

           // このページの中にエラーがあるか
           $scope.quiz.quizPage[pIdx].hasError = false;
           if (page.errorMessages) {
             $scope.quiz.quizPage[pIdx].hasError = true;
           }

           if (!page.quizQuestion) {
             continue;
           }

           // 各質問処理
           for (var qIdx = 0; qIdx < page.quizQuestion.length; qIdx++) {
             var question = $scope.quiz.quizPage[pIdx].quizQuestion[qIdx];

             // この質問の中にエラーがあるか
             if (question.errorMessages) {
               $scope.quiz.quizPage[pIdx].quizQuestion[qIdx].hasError = true;
               $scope.quiz.quizPage[pIdx].hasError = true;
             }

             if (question.quizCorrect) {
               for (var cIdx = 0; cIdx < question.quizCorrect.length; cIdx++) {
                 var correct = question.quizCorrect[cIdx];
                 if (correct.errorMessages) {
                   $scope.quiz.quizPage[pIdx].quizQuestion[qIdx].hasError = true;
                   $scope.quiz.quizPage[pIdx].hasError = true;
                 }
                 // 正解を未設定でエラーで戻ってきたときは空っぽになっているので
                 if (! correct.correct) {
                   correct.correct = new Array();
                 }
                 // Wizardの行き来中は択一選択とかだと文字列にしかなってないので
                 if (typeof correct.correct == 'string') {
                   correct.correct = correct.correct.split(variables.ANSWER_DELIMITER);
                 }
               }
             }
             // 選択肢がないのならここでcontinue;
             if (!question.quizChoice) {
               continue;
             }
             // 質問の選択肢の中にエラーがあるかのフラグ設定
             for (var cIdx = 0; cIdx < question.quizChoice.length; cIdx++) {
               var choice = question.quizChoice[cIdx];
               if (choice.errorMessages) {
                 $scope.quiz.quizPage[pIdx].quizQuestion[qIdx].hasError = true;
                 $scope.quiz.quizPage[pIdx].hasError = true;
               }
             }
           }
         }
         $scope.quiz.quizPage[0].tabActive = true;
       };
       /**
        * toArray
        *
        * 配列型のはずの変数がなぜかObject扱いになる場合があるので念のための変換
        * @return {Array}
        */
       $scope.toArray = function(src) {
         var dst = new Array();
         angular.forEach(src, function(obj, key) {
           obj = $scope._toArray(obj);
           dst[key] = obj;
         });
         return dst;
       };
       /**
        * _toArray
        *
        * toArrayの再帰関数
        * @return {Object}
        */
       $scope._toArray = function(src) {
         var dst = new Object();
         angular.forEach(src, function(obj, key) {
           if (key == 'quizQuestion' || key == 'quizChoice' || key == 'quizCorrect') {
             obj = $scope.toArray(obj);
           }
           dst[key] = obj;
         });
         return dst;
       };

       /**
        * ge allotment sum
        *
        * @return {void}
        */
       $scope.getAllotmentSum = function() {
         var sum = 0;
         for (var pIdx = 0; pIdx < $scope.quiz.quizPage.length; pIdx++) {
           var page = $scope.quiz.quizPage[pIdx];
           for (var qIdx = 0; qIdx < page.quizQuestion.length; qIdx++) {
             var question = page.quizQuestion[qIdx];
             if (question.allotment) {
               sum += parseInt(question.allotment);
             }
           }
         }
         return sum;
       };
       /**
        * is correct (for multiple choice)
        *
        * @return {void}
        */
       $scope.isCorrect = function(needle, haystack) {
         var len = haystack.length;
         for (var i = 0; i < len; i++) {
           if (haystack[i] == needle) {
             return true;
           }
         }
         return false;
       };

       /**
        * focus DateTimePicker
        *
        * @return {void}
        */
       $scope.setMinMaxDate = function(ev, pIdx, qIdx) {
         // 自分のタイプがMinかMaxかを知る
         var curEl = ev.currentTarget;
         var elId = curEl.id;

         var typeMinMax;
         typeMinMax = elId.substr(elId.lastIndexOf('.') + 1);
         var targetEl;
         var targetElId;

         // 相方のデータを取り出す
         if (typeMinMax == 'min') {
           targetElId = elId.substring(0, elId.lastIndexOf('.')) + '.max';
         } else {
           targetElId = elId.substring(0, elId.lastIndexOf('.')) + '.min';
         }
         var targetEl = document.getElementById(targetElId);
         var limitDate = $(targetEl).val();

         // 自分のMinまたはMaxを設定する
         var el = document.getElementById(elId);
         if (limitDate != '') {
           if (typeMinMax == 'min') {
             $(el).data('DateTimePicker').maxDate(limitDate);
           } else {
             $(el).data('DateTimePicker').minDate(limitDate);
           }
         }
       };

       /**
        * Add Quiz Page
        *
        * @return {void}
        */
       $scope.addPage = function($event) {
         // 既に質問数が上限に達している
         if ($scope.checkMaxQuestion() == false) {
           alert(quizzesMessages.maxQuestionWarningMsg);
           return;
         }
         var page = new Object();
         page['pageTitle'] = ($scope.quiz.quizPage.length + 1).toString(10);
         page['pageSequence'] = $scope.quiz.quizPage.length;
         page['key'] = '';
         page['isPageDescription'] = 0;
         page['pageDescription'] = '';
         page['quizQuestion'] = new Array();
         $scope.quiz.quizPage.push(page);

         $scope.addQuestion($event, $scope.quiz.quizPage.length - 1);

         $scope.quiz.quizPage[$scope.quiz.quizPage.length - 1].tabActive = true;
         if ($event) {
           $event.stopPropagation();
         }
       };

       /**
        * Delete Quiz Page
        *
        * @return {void}
        */
       $scope.deletePage = function(idx, message) {
         if ($scope.quiz.quizPage.length < 2) {
           // 残り１ページは削除させない
           return;
         }
         if (confirm(message)) {
           $scope.quiz.quizPage.splice(idx, 1);
           $scope._resetQuizPageSequence();
           // 削除された場合は１枚目のタブを選択するようにする
           $scope.quiz.quizPage[0].tabActive = true;
         }
       };

       /**
        * Quiz Page Sequence reset
        *
        * @return {void}
        */
       $scope._resetQuizPageSequence = function() {
         for (var i = 0; i < $scope.quiz.quizPage.length; i++) {
           $scope.quiz.quizPage[i].pageSequence = i;
         }
       };

       /**
        * Add Quiz Question
        *
        * @return {void}
        */
       $scope.addQuestion = function($event, pageIndex) {
         // 既に質問数が上限に達している
         if ($scope.checkMaxQuestion() == false) {
           alert(quizzesMessages.maxQuestionWarningMsg);
           return;
         }
         var question = new Object();
         if (!$scope.quiz.quizPage[pageIndex].quizQuestion) {
           $scope.quiz.quizPage[pageIndex].quizQuestion = new Array();
         }
         var newIndex = $scope.quiz.quizPage[pageIndex].quizQuestion.length;
         question['questionValue'] = quizzesMessages.newQuestionLabel + (newIndex + 1);
         question['questionSequence'] = newIndex;
         question['questionType'] = variables.TYPE_SELECTION;
         question['key'] = '';
         question['allotment'] = '10';
         question['commentary'] = '';
         question['isChoiceRandom'] = 0;
         question['isChoiceHorizon'] = 0;
         question['isOrderFixed'] = 0;
         question['quizChoice'] = new Array();
         question['quizCorrect'] = new Array();
         question['isOpen'] = true;
         $scope.quiz.quizPage[pageIndex].quizQuestion.push(question);

         var qIndex = $scope.quiz.quizPage[pageIndex].quizQuestion.length - 1;
         for (var itemCount = 0; itemCount < variables.DEFAULT_ITEM_COUNT; itemCount++) {
           $scope.addChoice($event, pageIndex, qIndex, itemCount);
         }
         $scope.addCorrect($event, pageIndex, qIndex);
         $scope.addCorrectWord($event, pageIndex, qIndex, 0, quizzesMessages.newChoiceLabel + 1);

         if ($event) {
           $event.stopPropagation();
         }
       };

       /**
        * Move Quiz Question
        *
        * @return {void}
        */
       $scope.moveQuestion = function($event, pageIndex, beforeIdxStr, afterIdxStr) {
         var beforeIdx = parseInt(beforeIdxStr);
         var afterIdx = parseInt(afterIdxStr);
         var beforeQ =
             $scope.quiz.quizPage[pageIndex].quizQuestion[beforeIdx];
         if (beforeIdx < afterIdx) {
           for (var i = beforeIdx + 1; i <= afterIdx; i++) {
             var tmpQ = $scope.quiz.quizPage[pageIndex].quizQuestion[i];
             $scope.quiz.quizPage[pageIndex].quizQuestion.splice(i - 1, 1, tmpQ);
           }
           $scope.quiz.quizPage[pageIndex].quizQuestion.splice(afterIdx, 1, beforeQ);
         }
         else {
           for (var i = beforeIdx; i >= afterIdx; i--) {
             var tmpQ = $scope.quiz.quizPage[pageIndex].quizQuestion[i - 1];
             $scope.quiz.quizPage[pageIndex].quizQuestion.splice(i, 1, tmpQ);
           }
           $scope.quiz.quizPage[pageIndex].quizQuestion.splice(afterIdx, 1, beforeQ);
         }
         $scope._resetQuizQuestionSequence(pageIndex);
         $event.preventDefault();
         $event.stopPropagation();
       };

       /**
        * Move to another page Quiz Question
        *
        * @return {void}
        */
       $scope.copyQuestionToAnotherPage = function($event, pageIndex, qIndex, copyPageIndex) {
         // 既に質問数が上限に達している
         if ($scope.checkMaxQuestion() == false) {
           alert(quizzesMessages.maxQuestionWarningMsg);
           return;
         }
         var tmpQ = angular.copy($scope.quiz.quizPage[pageIndex].quizQuestion[qIndex]);
         $scope.quiz.quizPage[copyPageIndex].quizQuestion.push(tmpQ);

         $scope._resetQuizQuestionSequence(copyPageIndex);
         //$event.stopPropagation();
       };
       /**
        * アコーディオンヘッダの中のドロップダウンメニューボタンのクリックで
        * アコーディオンが開閉するのを抑止するための
        *
        * @return {String}
        */
       $scope.deter = function($event) {
         $event.preventDefault();
         $event.stopPropagation();
       };

       /**
        * Delete Quiz Question
        *
        * @return {void}
        */
       $scope.deleteQuestion = function($event, pageIndex, idx, message) {
         if ($scope.quiz.quizPage[pageIndex].quizQuestion.length < 2) {
           return;
         }
         if (confirm(message)) {
           $scope.quiz.quizPage[pageIndex].quizQuestion.splice(idx, 1);
           $scope._resetQuizQuestionSequence(pageIndex);
         }
         // ここでやってはいけない！ページの再読み込みが走る
         //$event.stopPropagation();
       };

       /**
        * Quiz Question Sequence reset
        *
        * @return {void}
        */
       $scope._resetQuizQuestionSequence = function(pageIndex) {
         for (var i = 0; i < $scope.quiz.quizPage[pageIndex].quizQuestion.length; i++) {
           $scope.quiz.quizPage[pageIndex].quizQuestion[i].questionSequence = i;
         }
       };

       /**
        * Add Quiz Choice
        *
        * @return {void}
        */
       $scope.addChoice = function($event, pIdx, qIdx, choiceCount) {
         var page = $scope.quiz.quizPage[pIdx];
         var question = $scope.quiz.quizPage[pIdx].quizQuestion[qIdx];
         var choice = new Object();

         if (!question.quizChoice) {
           $scope.quiz.quizPage[pIdx].quizQuestion[qIdx].quizChoice = new Array();
         }
         var newIndex = question.quizChoice.length;

         // 選択肢設置数上限
         if (newIndex == variables.MAX_CHOICE_COUNT) {
           alert(quizzesMessages.maxChoiceWarningMsg);
           return;
         }

         choice['choiceSequence'] = newIndex;
         choice['choiceLabel'] = quizzesMessages.newChoiceLabel + (choiceCount + 1);
         choice['key'] = '';

         // 指定された新しい選択肢を追加する
         $scope.quiz.quizPage[pIdx].quizQuestion[qIdx].quizChoice.push(choice);

         if ($event != null) {
           $event.stopPropagation();
         }
       };
       /**
        * Delete Quiz Choice
        *
        * @return {void}
        */
       $scope.deleteChoice = function($event, pIdx, qIdx, seq) {
         var question = $scope.quiz.quizPage[pIdx].quizQuestion[qIdx];

         if (question.quizChoice.length < 2) {
           return;
         }
         for (var i = 0; i < question.quizChoice.length; i++) {
           if (question.quizChoice[i].choiceSequence == seq) {
             $scope.quiz.quizPage[pIdx].quizQuestion[qIdx].quizChoice.splice(i, 1);
           }
         }
         $scope._resetQuizChoiceSequence(pIdx, qIdx);

         if ($event) {
           $event.stopPropagation();
         }
       };
       /**
        * Quiz Choice Sequence reset
        *
        * @return {void}
        */
       $scope._resetQuizChoiceSequence = function(pageIndex, qIndex) {
         var choiceLength =
             $scope.quiz.quizPage[pageIndex].quizQuestion[qIndex].quizChoice.length;
         for (var i = 0; i < choiceLength; i++) {
           $scope.quiz.quizPage[pageIndex].quizQuestion[qIndex].quizChoice[i].choiceSequence = i;
         }
       };

       /**
        * add correct
        *
        * @return {void}
        */
       $scope.addCorrect = function($event, pIdx, qIdx) {
         var page = $scope.quiz.quizPage[pIdx];
         var question = $scope.quiz.quizPage[pIdx].quizQuestion[qIdx];
         var correct = new Object();

         if (!question.quizCorrect) {
           $scope.quiz.quizPage[pIdx].quizQuestion[qIdx].quizCorrect = new Array();
         }
         var newIndex = question.quizCorrect.length;

         // 回答単語欄設置数上限
         if (newIndex == variables.MAX_CHOICE_COUNT) {
           alert(quizzesMessages.maxChoiceWarningMsg);
           return;
         }

         correct['correctSequence'] = newIndex;
         correct['correct'] = new Array();
         correct['newWordCorrect'] = '';

         // 指定された新しい正解を追加する
         $scope.quiz.quizPage[pIdx].quizQuestion[qIdx].quizCorrect.push(correct);

         if ($event != null) {
           $event.stopPropagation();
         }
       };
       /**
        * delete correct
        *
        * @return {void}
        */
       $scope.deleteCorrect = function($event, pIdx, qIdx, cIdx) {
         var page = $scope.quiz.quizPage[pIdx];
         var question = $scope.quiz.quizPage[pIdx].quizQuestion[qIdx];

         question.quizCorrect.splice(cIdx, 1);
       };
       /**
        * add correct word
        *
        * @return {void}
        */
       $scope.addCorrectWord = function($event, pIdx, qIdx, correctIndex, correctLabel) {
         if (!correctLabel || correctLabel.length == 0) {
           alert(quizzesMessages.warningCorrectWordAdd);
           return;
         }
         var page = $scope.quiz.quizPage[pIdx];
         var question = $scope.quiz.quizPage[pIdx].quizQuestion[qIdx];
         var correct = question.quizCorrect[correctIndex];
         correct.correct.push(correctLabel);
         correct.newWordCorrect = '';
       };
       /**
        * remove correct word
        *
        * @return {void}
        */
       $scope.removeCorrectWord = function($event, pIdx, qIdx, correctIndex, correctLabel) {
         var page = $scope.quiz.quizPage[pIdx];
         var question = $scope.quiz.quizPage[pIdx].quizQuestion[qIdx];
         var correct = question.quizCorrect[correctIndex];
         var index = correct.correct.indexOf(correctLabel);

         correct.correct.splice(index, 1);
       };

       /**
        * change Question Type
        *
        * @return {void}
        */
       $scope.changeQuestionType = function($event, pIdx, qIdx) {
         var question = $scope.quiz.quizPage[pIdx].quizQuestion[qIdx];
         var choice = $scope.quiz.quizPage[pIdx].quizQuestion[qIdx].quizChoice;
         var correct = $scope.quiz.quizPage[pIdx].quizQuestion[qIdx].quizCorrect;

         // テキストなどのタイプから選択肢などに変更されたとき
         // 選択肢要素が一つもなくなっている場合があるので最低一つは存在するように
         if (! choice || choice.length == 0) {
           for (var itemCount = 0; itemCount < variables.DEFAULT_ITEM_COUNT; itemCount++) {
             $scope.addChoice($event, pIdx, qIdx, itemCount);
           }
         }
         if (! correct || correct.length == 0) {
           $scope.addCorrect($event, pIdx, qIdx);
         }
         if (question.questionType == variables.TYPE_MULTIPLE_WORD) {
           if (correct.length < variables.DEFAULT_ITEM_COUNT) {
             for (var itemCt = correct.length; itemCt < variables.DEFAULT_ITEM_COUNT; ) {
               $scope.addCorrect($event, pIdx, qIdx);
               itemCt = $scope.quiz.quizPage[pIdx].quizQuestion[qIdx].quizCorrect.length;
             }
           }
         }
       };
        /**
         * 現在の質問数に＋１したらMAXを超えてしまうかどうかのガード
         *
         * @return {bool}
         */
        $scope.checkMaxQuestion = function() {
          var ct = 0;
          var pageArr = $scope.quiz.quizPage;
          for (var i = 0; i < pageArr.length; i++) {
            ct += pageArr[i].quizQuestion.length;
          }
          if (ct + 1 > variables.MAX_QUESTION_COUNT) {
            return false;
          }
          return true;
        };
     }]);
