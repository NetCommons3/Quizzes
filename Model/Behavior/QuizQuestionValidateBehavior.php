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
class QuizQuestionValidateBehavior extends ModelBehavior {

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
		// 付属の選択肢以下のvalidate
		if ($this->_checkChoiceExists($model)) {
			// この質問種別に必要な選択肢データがちゃんとあるなら選択肢をバリデート
			$validationErrors = array();
			foreach ($model->data['QuizChoice'] as $cIndex => $choice) {
				// 選択肢データ確認
				$model->QuizChoice->create();
				$model->QuizChoice->set($choice);
				$options['choiceIndex'] = $cIndex;
				if (! $model->QuizChoice->validates($options)) {
					$validationErrors['QuizChoice'][$cIndex] = $model->QuizChoice->validationErrors;
				}
			}
			$model->validationErrors += $validationErrors;
		}

		if ($this->_checkCorrectExists($model)) {
			foreach ($model->data['QuizCorrect'] as $correct) {
				$model->QuizCorrect->create();
				$model->QuizCorrect->set($correct);
				if (! $model->QuizCorrect->validates()) {
					$model->validationErrors['question_pickup_error'][] =
						__d('quizzes', 'please set at least one correct.');
				}
			}
		}
		// このPickupErrorはAngularのng-repeatで処理するので、
		// 同じメッセージがあるとエラーになっちゃう
		if (! empty($model->validationErrors['question_pickup_error'])) {
			$pickupError = array_flip($model->validationErrors['question_pickup_error']);
			$model->validationErrors['question_pickup_error'] = array_flip($pickupError);
		}
	}
/**
 * _checkChoiceExists
 *
 * 適正な選択肢を持っているか
 *
 * @param Model $model Model using this behavior
 * @return bool
 */
	protected function _checkChoiceExists($model) {
		$questionType = $model->data['QuizQuestion']['question_type'];
		// 単語系、記述式の時は選択肢不要
		if (! QuizzesComponent::isSelectionInputType($questionType)) {
			return false;
		}
		// 上記以外の場合は最低１つは必要
		if (! Hash::check($model->data, 'QuizChoice.{n}')) {
			$model->validationErrors['question_pickup_error'][] =
				__d('quizzes', 'please set at least one choice.');
			return false;
		}
		return true;
	}
/**
 * _checkCorrectExists
 *
 * 適正な正解を持っているか
 *
 * @param Model $model Model using this behavior
 * @return bool
 */
	protected function _checkCorrectExists($model) {
		$questionType = $model->data['QuizQuestion']['question_type'];
		// 記述式の時は正解不要
		if ($questionType == QuizzesComponent::TYPE_TEXT_AREA) {
			return false;
		}
		// 上記以外の場合は最低１つは必要
		if (! Hash::check($model->data, 'QuizCorrect.{n}')) {
			$model->validationErrors['question_pickup_error'][] =
				__d('quizzes', 'please set at least one correct.');
			return false;
		}
		return true;
	}
}