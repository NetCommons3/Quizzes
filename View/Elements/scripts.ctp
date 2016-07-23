<?php
echo $this->NetCommonsHtml->script(array(
));
echo $this->NetCommonsHtml->scriptBlock(
    'NetCommonsApp.constant("quizzesMessages", {' .
        '"newQuestionLabel": "' . __d('quizzes', 'New Question') . '",' .
        '"newChoiceLabel": "' . __d('quizzes', 'New Choice') . '",' .
        '"warningCorrectWordAdd": "' . __d('quizzes', 'Please input some word') . '",' .
        '"resultScoreLabel": "' . __d('quizzes', 'Score') . '",' .
        '"resultPersonsLabel": "' . __d('quizzes', 'Persons') . '",' .
        '"resultNumberLabel": "' . __d('quizzes', 'Number') . '"' .
    '});'
);
echo $this->NetCommonsHtml->css('/quizzes/css/quiz.css');

