<?php
echo $this->NetCommonsHtml->script(array(
));
$maxQuestionWarningMsg = sprintf(
	__d('quizzes', 'Number of questions that can be created is up %d . Already it has become %d .'),
	QuizzesComponent::MAX_QUESTION_COUNT,
	QuizzesComponent::MAX_QUESTION_COUNT
);
$maxChoiceWarningMsg = sprintf(
	__d('quizzes', 'Number of choices that can be created is up %d per question. Already it has become %d .'),
	QuizzesComponent::MAX_CHOICE_COUNT,
	QuizzesComponent::MAX_CHOICE_COUNT
);

echo $this->NetCommonsHtml->scriptBlock(
	'NetCommonsApp.constant("quizzesMessages", {' .
		'"newQuestionLabel": "' . __d('quizzes', 'New Question') . '",' .
		'"newChoiceLabel": "' . __d('quizzes', 'New Choice') . '",' .
		'"warningCorrectWordAdd": "' . __d('quizzes', 'Please input some word') . '",' .
		'"resultScoreLabel": "' . __d('quizzes', 'Score') . '",' .
		'"resultPersonsLabel": "' . __d('quizzes', 'Persons') . '",' .
		'"resultNumberLabel": "' . __d('quizzes', 'Number') . '",' .
		'"maxQuestionWarningMsg": "' . $maxQuestionWarningMsg . '",' .
		'"maxChoiceWarningMsg": "' . $maxChoiceWarningMsg . '",' .
	'});'
);
echo $this->NetCommonsHtml->css('/quizzes/css/quiz.css');

