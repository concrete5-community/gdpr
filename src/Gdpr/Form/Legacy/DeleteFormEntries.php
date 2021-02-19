<?php

namespace A3020\Gdpr\Form\Legacy;

use Concrete\Core\Database\Connection\Connection;
use DateTime;

class DeleteFormEntries
{
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Remove all legacy form submissions
     *
     * We can pass a date, e.g. to only remove submission
     * that are older than a week. By default all submissions are removed.
     *
     * @param array $options
     *
     * @throws \Doctrine\ORM\ORMException
     *
     * @return int
     */
    public function delete($options = [])
    {
        $submissionsRemoved = 0;

        $options += [
            'created_before' => new DateTime('+50 years'),
        ];

        foreach ($this->db->fetchAll('SELECT questionSetId FROM btForm') as $form) {
            $submissionsRemoved += $this->deleteFormAnswers($form['questionSetId'], $options['created_before']);
        }

        return $submissionsRemoved;
    }

    /**
     * Remove all submissions of a certain form
     *
     * @param int $questionSetId
     * @param DateTime $createdBefore
     *
     * @return int
     */
    private function deleteFormAnswers($questionSetId, $createdBefore)
    {
        $formSubmissions = $this->db->fetchAll('SELECT asID FROM btFormAnswerSet WHERE questionSetId = ? AND created < ?', [
            (int) $questionSetId,
            $createdBefore->format('Y-m-d H:i:s'),
        ]);

        // Remove the individual answers of each submission
        foreach ($formSubmissions as $formSubmission) {
            $this->deleteAnswers($formSubmission['asID']);
        }

        // Remove all submissions of this form
        $this->db->executeQuery('DELETE FROM btFormAnswerSet WHERE asID = ?', [
            (int) $formSubmission['asID'],
        ]);

        return count($formSubmissions);
    }

    /**
     * Remove form values of a single submission
     *
     * @param int $answerSetId
     */
    private function deleteAnswers($answerSetId)
    {
        $this->db->executeQuery('DELETE FROM btFormAnswers WHERE asID = ?', [
            (int) $answerSetId,
        ]);
    }
}
