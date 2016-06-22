<?php
/**
 * QuizzesShuffle Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * QuizzesShuffleComponent
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Controller
 */
class QuizzesShuffleComponent extends Component {

/**
 * shufflePage
 * shuffled pages and write into session
 *
 * @param array &$quiz 小テスト
 * @return void
 */
	public function shufflePage(&$quiz) {
		$session = $this->_Collection->load('Session');
		$pages = $quiz['QuizPage'];
		$sessionPath = 'Quizzes.quizShuffles.' . $quiz['Quiz']['key'] . '.QuizPage';
		if ($session->check($sessionPath)) {
			$pages = $session->read($sessionPath);
		} else {
			if ($quiz['Quiz']['is_page_random'] == QuizzesComponent::USES_USE) {
				shuffle($pages);
			}
			$serialNumber = 0;
			foreach ($pages as &$page) {
				foreach ($page['QuizQuestion'] as &$question) {
					$question['serial_number'] = $serialNumber++;
				}
			}
			$session->write($sessionPath, $pages);
		}
		$quiz['QuizPage'] = $pages;
	}

/**
 * getNextPage
 * get next page from shuffled pages
 *
 * @param array $quiz 小テスト
 * @param int $nowPageSeq 現在のページ順番
 * @return int 次のページのインデックス
 */
	public function getNextPage($quiz, $nowPageSeq) {
		$session = $this->_Collection->load('Session');
		$sessionPath = 'Quizzes.quizShuffles.' . $quiz['Quiz']['key'] . '.QuizPage';
		$pages = $session->read($sessionPath);
		if (! $pages) {
			$pages = $quiz['QuizPage'];
		}
		foreach ($pages as $index => $page) {
			if ($page['page_sequence'] == $nowPageSeq) {
				if (isset($pages[$index + 1])) {
					//return $pages[$index + 1]['page_sequence'];
					return $index + 1;
				} else {
					return false;
				}
			}
		}
		return false;
	}

/**
 * shuffleChoice
 * shuffled choices and write into session
 *
 * @param array &$quiz 小テスト
 * @return void
 */
	public function shuffleChoice(&$quiz) {
		$session = $this->_Collection->load('Session');
		foreach ($quiz['QuizPage'] as &$page) {
			foreach ($page['QuizQuestion'] as &$q) {
				if (! QuizzesComponent::isSelectionInputType($q['question_type'])) {
					continue;
				}
				$choices = $q['QuizChoice'];
				if ($q['is_choice_random'] == QuizzesComponent::USES_USE) {
					$sessionPath =
						'Quizzes.quizShuffles.' . $quiz['Quiz']['key'] .
						'.QuizQuestion.' . $q['key'] . '.QuizChoice';
					if ($session->check($sessionPath)) {
						$choices = $session->read($sessionPath);
					} else {
						shuffle($choices);
						$session->write($sessionPath, $choices);
					}
				}
				$q['QuizChoice'] = $choices;
			}
		}
	}
/**
 * clear
 *
 * @param string $quizKey quiz key
 * @return void
 */
	public function clear($quizKey = '') {
		$sessionPath = 'Quizzes.quizShuffles';
		if (! empty($quizKey)) {
			$sessionPath .= '.' . $quizKey;
		}
		$session = $this->_Collection->load('Session');
		$session->delete($sessionPath);
	}
}
