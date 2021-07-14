<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


class MemberFormPresenter extends Nette\Application\UI\Presenter
{
	private Nette\Database\Explorer $database;
    private array $types;


    public function __construct(Nette\Database\Explorer $database, array $types = [])
	{
		$this->database = $database;
        $this->types = $types;
	}

    public function actionDefault(): void 
    {
        $this->template->members = $this->database->fetchAll('SELECT q1.memberId, q1.first_name, q1.last_name, q1.type, q2.team_name
        FROM(SELECT tm.memberId, tm.first_name, tm.last_name, ty.name as type 
            FROM team_members as tm left join types as ty on ty.id = tm.types_id) q1
        left join(SELECT tm.memberid, t.name as team_name
            FROM team_members as tm left join teams as t on t.id = tm.team_id) q2 
        on ( q1.memberId = q2.memberId)');


        $pairs = $this->database->fetchPairs('SELECT id,name FROM types');

        foreach($pairs as $id => $value){
            array_push($this->types,$value);
        }
        array_unshift($this->types,'Vyber typ člena týmu');

    }

    public function actionShow(): void
    {
        $this->redirect('MemberForm:default');
    }
    
    public function actionDeleteMember(int $memberId): void
    {
        $member = $this->database->table('team_members')->get($memberId);
        if(!$member) { 
            $this->error('Člen nebyl nalezen');
        }

        $this->database->table('team_members')
            ->where('memberId',$memberId)
            ->delete();

        $this->redirect('MemberForm:');
    }

    protected function createComponentMemberForm(): Form
    {
        $form = new Form;
        $form->addText('first_name', 'Jméno:')
            ->setRequired();
        $form->addText('last_name', 'Příjmení:')
            ->setRequired();
        $form->addSelect('types_id', 'Typ:',$this->types)
            ->setRequired();
        $form->addSubmit('send', 'Uložit člena');
        $form->onSuccess[] = [$this, 'memberFormSucceeded'];

        return $form;
    }

    public function memberFormSucceeded(Form $form, array $values): void
    {
        $memberId = $this->getParameter('id');
        if($values['types_id'] == 0){
            $this->flashMessage('Pro vytvoření člena týmu je potřeba zadat jeho typ/pozci');
            $this->redirect('MemberForm:');
        }
        if($memberId){
            $member->$this->database->table('team_members')->get($memberId);
            $member->update($values);
        } else {
            $this->database->table('team_members')->insert([
                'first_name' => $values['first_name'],
                'last_name' => $values['last_name'],
                'types_id' => $values['types_id'],
                'team_id' => NULL
            ]); 
        }
        $this->flashMessage('Byl vytvořen nový člen týmu');
    }
}
