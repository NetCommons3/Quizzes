<?php
/**
 * QuizQuestionValidate Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * QuizQuestionChoice Behavior
 *
 * @package  Quizzes\Quizzes\Model\Befavior\QuizQuestionChoice
 * @author Allcreator <info@allcreator.net>
 */
class QuizAnswerValidateBehavior extends ModelBehavior {

/**
 * setup
 *
 * @param Model $Model モデル
 * @param array $settings 設定値
 * @return void
 */
	public function setup(Model $Model, $settings = array()) {
		$this->settings[$Model->alias] = $settings;
	}

/**
 * beforeValidate is called before a model is validated, you can use this callback to
 * add behavior validation rules into a models validate array. Returning false
 * will allow you to make the validation fail.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False or null will abort the operation. Any other result will continue.
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = array()) {
		// 未記入は無条件に許す
		// 「テスト」ですからね...わからなくて答えられないこともありますよ
		if (empty($model->data['QuizAnswer']['answer_value'])) {
			return true;
		}
		$question = $options['question'];
		// 選択肢タイプのときは選択肢の中の答えであること
		if (QuizzesComponent::isSelectionInputType($question['question_type'])) {
			$answers = explode(
				QuizzesComponent::ANSWER_DELIMITER,
				$model->data['QuizAnswer']['answer_value']
			);
			$choice = hash::extract($question['QuizChoice'], '{n}.choice_label');
			foreach ($answers as $answer) {
				// 基本的に選択肢以外の入力が来てるってないはずなので
				if (! in_array($answer, $choice)) {
					$model->validationErrors['answer_value'][] =
						__d('net_commons', 'Invalid request.');
					return false;
				}
			}
		}
		return true;
	}

}


