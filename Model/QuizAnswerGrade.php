<?php
/**
 * QuizAnswerGrade Model
 *
 * @property QuizAnswerGrade $QuizAnswerGrade
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('QuizzesAppModel', 'Quizzes.Model');

/**
 * QuizAnswerGrade Model
 */
class QuizAnswerGrade extends QuizzesAppModel {

/**
 * Use table config
 *
 * @var bool
 */
	public $useTable = 'quiz_answers';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'correct_status' => array(
			'isCorrect' => array(
				'rule' => array('inList', array(0, 1, 2)),
			)
		),
		'score' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'QuizAnswerSummary' => array(
			'className' => 'Quizzes.QuizAnswerSummary',
			'foreignKey' => 'quiz_answer_summary_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * 採点の保存
 *
 * @param array $quiz 小テストデータ
 * @param int $summaryId サマリID
 * @param array $datas 採点データ配列
 * @return bool
 * @throws InternalErrorException
 */
	public function saveGrade($quiz, $summaryId, $datas) {
		$this->loadModels([
			'QuizAnswer' => 'Quizzes.QuizAnswer',
			'QuizAnswerSummary' => 'Quizzes.QuizAnswerSummary',
		]);
		$this->begin();
		foreach ($datas as $data) {
			$answer = $data['QuizAnswerGrade'];
			if (! $this->save($answer)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}
		// 未採点があるときはnullが返ってきます
		$score = $this->QuizAnswer->getScore($summaryId);

		$summaryData = array();
		$summaryData['id'] = $summaryId;
		// 採点完了
		if (! is_null($score)) {
			$summaryData['summary_score'] = $score;
			$summaryData['is_grade_finished'] = true;
			if ($quiz['Quiz']['passing_grade'] > 0 && $score >= $quiz['Quiz']['passing_grade']) {
				$summaryData['passing_status'] = QuizzesComponent::STATUS_GRADE_PASS;
			} else {
				$summaryData['passing_status'] = QuizzesComponent::STATUS_GRADE_FAIL;
			}
		} else {
			$summaryData['is_grade_finished'] = false;
			$summaryData['passing_status'] = QuizzesComponent::STATUS_GRADE_YET;
		}
		if (! $this->QuizAnswerSummary->save($summaryData, false, array(
			'summary_score',
			'is_grade_finished',
			'passing_status'))) {
			throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
		}
		$this->commit();
	}
}
