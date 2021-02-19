<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\DataBreach;

use A3020\Gdpr\Controller\DashboardController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Exception;

final class NotifyUsers extends DashboardController
{
    public function view()
    {
        $fromName = $this->config->get('concrete.email.default.name');

        $this->set('fromName', $fromName ? $fromName : $this->config->get('site.sites.default.name'));
        $this->set('fromEmail', $this->config->get('concrete.email.default.address'));

        $this->set('userGroups', $this->getUserGroups());
        $this->set('defaultMessage', t('We value your business and respect the privacy of your information, which is why, as a '.
            'precautionary measure, we are writing to let you know about a data security incident that [may involve/involves] your personal information.')
        );
    }

    public function send()
    {
        $group = Group::getByID($this->post('user_group'));
        if (!$group) {
            $this->flash('error', t('Invalid user group'));
            return $this->action('/dashboard/gdpr/data_breach/notify_users');
        }

        try {
            $userEmails = $this->getUserEmails($group->getGroupMemberIDs());
            $this->sendEmail(
                $this->post('fromName'),
                $this->post('fromEmail'),
                $this->post('subject'),
                $this->post('message'),
                $userEmails
            );
        } catch (Exception $e) {
            $this->error = t('Something went wrong: '.$e->getMessage());

            return $this->view();
        }

        $this->flash('success', t2(
            'The notification has been sent to %s user.',
            'The notification has been sent to %s users.',
            $group->getGroupMembersNum()
        ));

        return $this->action('/dashboard/gdpr/data_breach/notify_users/sent');
    }

    public function sent()
    {
        $this->set('sent', true);
    }

    /**
     * @return array
     */
    private function getUserGroups()
    {
        $gl = new GroupList();
        $gl->includeAllGroups();

        $groups = ['' => t('-- Please select --')];
        foreach ($gl->getResults() as $group) {
            /** @var Group $group */
            if ($group->getGroupID() === GUEST_GROUP_ID) {
                continue;
            }

            $numberOfUsers = (int) $group->getGroupMembersNum();
            if ($numberOfUsers === 0) {
                continue;
            }

            $groups[$group->getGroupID()] = $group->getGroupDisplayName() .' ('.t2('%s user', '%s users', $numberOfUsers).')';
        }

        return $groups;
    }

    /**
     * @param string $fromName
     * @param string $fromEmail
     * @param string $subject
     * @param string $message
     * @param array $userEmails
     *
     * @throws Exception
     */
    private function sendEmail($fromName, $fromEmail, $subject, $message, $userEmails)
    {
        /** @var \Concrete\Core\Mail\Service $mail */
        $mail = $this->app->make('mail');

        $mail->from($fromEmail, $fromName);
        $mail->setSubject($subject);
        $mail->setBodyHTML($message);

        foreach ($userEmails as $email) {
            $mail->bcc($email);
        }

        $mail->sendMail();
    }

    /**
     * @param array $userIds
     *
     * @return array
     */
    private function getUserEmails($userIds)
    {
        /** @var Connection $db */
        $db = $this->app['database']->connection();

        $userIds = array_map('intval', $userIds);
        $qMarks = str_repeat('?,', count($userIds) - 1) . '?';

        return $db->fetchAssoc("SELECT uEmail FROM Users WHERE uID IN (".$qMarks.")", $userIds);
    }
}
