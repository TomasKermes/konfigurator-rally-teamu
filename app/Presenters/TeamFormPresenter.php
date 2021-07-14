<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

class TeamFormPresenter extends Nette\Application\UI\Presenter
{
	private Nette\Database\Explorer $database;
    private array $membersArr;
    private array $racersArr;
    private array $coDriversArr;
    private array $techniciansArr;
    private array $managersArr;
    private array $photographersArr;


    public function __construct(Nette\Database\Explorer $database, array $membersArr = [], array $racersArr = [], array $coDriversArr = [], array $techniciansArr = [], array $managersArr = [], array $photographersArr = [])
	{
		$this->database = $database;
        $this->membersArr = $membersArr;
        $this->racersArr = $racersArr;
        $this->coDriversArr = $coDriversArr;
        $this->techniciansArr = $techniciansArr;
        $this->managersArr = $managersArr;
        $this->photographersArr = $photographersArr;

	}

    public function actionShow(): void
    {
        $this->redirect('TeamForm:default');
    }

    public function actionDefault(): void 
    {
        $this->template->teams = $this->database->fetchAll('SELECT * FROM teams');

        $this->template->teamMembers= $this->database->fetchAll('SELECT q1.memberId, q1.first_name, q1.last_name, q1.type, q2.team_name
        FROM(SELECT tm.memberId, tm.first_name, tm.last_name, ty.name as type 
            FROM team_members as tm left join types as ty on ty.id = tm.types_id) q1
        left join(SELECT tm.memberid, t.name as team_name
            FROM team_members as tm left join teams as t on t.id = tm.team_id) q2 
        on ( q1.memberId = q2.memberId)');

        $this->template->members = $this->database->fetchAll('SELECT q1.memberId, q1.first_name, q1.last_name, q1.type, q2.team_name
        FROM(SELECT tm.memberId, tm.first_name, tm.last_name, ty.name as type 
            FROM team_members as tm left join types as ty on ty.id = tm.types_id) q1
        left join(SELECT tm.memberid, t.name as team_name
            FROM team_members as tm left join teams as t on t.id = tm.team_id
            where tm.team_id is not null) q2 
        on ( q1.memberId = q2.memberId)');

        $myMemberArr = array();
        foreach($this->template->members as $key => $value){   
            $newkey = sprintf('%s',$value->first_name.$value->last_name);
            $myMemberArr[$newkey] = $value;
        }
        $this->membersArr = $myMemberArr;


        $this->template->racers = $this->database->fetchAll('SELECT q1.memberId, q1.first_name, q1.last_name, q1.type, q2.team_name
        FROM(SELECT tm.memberId, tm.first_name, tm.last_name, ty.name as type 
            FROM team_members as tm left join types as ty on ty.id = tm.types_id
            where ty.id = 1) q1
        left join(SELECT tm.memberid, t.name as team_name
            FROM team_members as tm left join teams as t on t.id = tm.team_id
            where tm.team_id is null) q2 
        on ( q1.memberId = q2.memberId);');

        foreach($this->template->racers as $id => $value)
        {      
            array_push($this->racersArr, $value->first_name . " " . $value->last_name);
        }


        $this->template->coDrivers = $this->database->fetchAll('SELECT q1.memberId, q1.first_name, q1.last_name, q1.type, q2.team_name
        FROM(SELECT tm.memberId, tm.first_name, tm.last_name, ty.name as type 
            FROM team_members as tm left join types as ty on ty.id = tm.types_id
            where ty.id = 2) q1
        left join(SELECT tm.memberid, t.name as team_name
            FROM team_members as tm left join teams as t on t.id = tm.team_id
            where tm.team_id is null) q2 
        on ( q1.memberId = q2.memberId);');

        foreach($this->template->coDrivers as $id => $value)
        {      
            array_push($this->coDriversArr, $value->first_name . " " . $value->last_name);
        }


        $this->template->technicians = $this->database->fetchAll('SELECT q1.memberId, q1.first_name, q1.last_name, q1.type, q2.team_name
        FROM(SELECT tm.memberId, tm.first_name, tm.last_name, ty.name as type 
            FROM team_members as tm left join types as ty on ty.id = tm.types_id
            where ty.id = 3) q1
        left join(SELECT tm.memberid, t.name as team_name
            FROM team_members as tm left join teams as t on t.id = tm.team_id
            where tm.team_id is null) q2 
        on ( q1.memberId = q2.memberId);');

        foreach($this->template->technicians as $id => $value)
        {      
            array_push($this->techniciansArr, $value->first_name . " " . $value->last_name);
        }


        $this->template->managers = $this->database->fetchAll('SELECT q1.memberId, q1.first_name, q1.last_name, q1.type, q2.team_name
        FROM(SELECT tm.memberId, tm.first_name, tm.last_name, ty.name as type 
            FROM team_members as tm left join types as ty on ty.id = tm.types_id
            where ty.id = 4) q1
        left join(SELECT tm.memberid, t.name as team_name
            FROM team_members as tm left join teams as t on t.id = tm.team_id
            where tm.team_id is null) q2 
        on ( q1.memberId = q2.memberId);');

        foreach($this->template->managers as $id => $value)
        {      
            array_push($this->managersArr, $value->first_name . " " . $value->last_name);
        }


        $this->template->photographers = $this->database->fetchAll('SELECT q1.memberId, q1.first_name, q1.last_name, q1.type, q2.team_name
        FROM(SELECT tm.memberId, tm.first_name, tm.last_name, ty.name as type 
            FROM team_members as tm left join types as ty on ty.id = tm.types_id
            where ty.id = 5) q1
        left join(SELECT tm.memberid, t.name as team_name
            FROM team_members as tm left join teams as t on t.id = tm.team_id
            where tm.team_id is null) q2 
        on ( q1.memberId = q2.memberId);');

        foreach($this->template->photographers as $id => $value)
        {      
            array_push($this->photographersArr, $value->first_name . " " . $value->last_name);
        }


    }

    public function actionDeleteTeam(int $teamId): void
    {
        $teamId = $this->database->table('teams')->get($teamId);
        if(!$teamId) { 
            $this->error('Tým nebyl nalezen');
        }

        $this->database->table('teams')
            ->where('id',$teamId)
            ->delete();

        $this->redirect('TeamForm:');
    }


    protected function createComponentTeamForm(): Form
    {
        $form = new Form;

        $form->addText('name', 'Jméno týmu:')
        
            ->setRequired();

        $form->addMultiSelect('zavo','Závodník*:', $this->racersArr)
        ->addRule($form::MAX_LENGTH, 'Heslo musí mít alespoň %d znaků', 3)
        ->setRequired();

        $form->addMultiSelect('spolu','Spolujezdec*:',$this->coDriversArr)
        ->setRequired();

        $form->addMultiSelect('tech','Technik*:',$this->techniciansArr)
        ->setRequired();


        $form->addMultiSelect('mana','Manažer*:',$this->managersArr)
        ->setRequired();

        $form->addMultiSelect('foto','Fotograf:',$this->photographersArr);



        $form->addSubmit('send', 'Uložit tým');
        $form->onSuccess[] = [$this, 'teamFormSucceeded'];

        return $form;
    }
    
    
    public function teamFormSucceeded(Form $form,array $values): void
    {
        $row = $this->database->table('teams')->insert([
            'name' => $values['name'],
        ]); 

        $teamId = $row->id;

        unset($values['name']);


        foreach($values as $key => $value){
            
            foreach($value as $subKey => $subVal){
                if($key == 'zavo'){
                    $member = $this->membersArr[$this->racersArr[$subKey]];
                    $this->database->table('team_members')
                    ->where('memberId',$member['memberId'])
                    ->update(['team_id' => $teamId]);
                }
                if($key == 'spolu'){
                    $member = $this->membersArr[$this->coDriversArr[$subKey]];
                    $this->database->table('team_members')
                    ->where('memberId',$member['memberId'])
                    ->update(['team_id' => $teamId]);
                }
                if($key == 'tech'){
                    $member = $this->membersArr[$this->techniciansArr[$subKey]];
                    $this->database->table('team_members')
                    ->where('memberId',$member['memberId'])
                    ->update(['team_id' => $teamId]);
                }
                if($key == 'mana'){
                    $member = $this->membersArr[$this->managersArr[$subKey]];
                    $this->database->table('team_members')
                    ->where('memberId',$member['memberId'])
                    ->update(['team_id' => $teamId]);
                }
                if($key == 'foto'){
                    $member = $this->membersArr[$this->photographersArr[$subKey]];
                    $this->database->table('team_members')
                    ->where('memberId',$member['memberId'])
                    ->update(['team_id' => $teamId]);
                }

            }
        }

        $this->flashMessage('Byl vytvořen nový tým');
        $this->redirect('TeamForm:');
    }
}