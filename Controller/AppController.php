<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;
//session_start();
use Cake\Controller\Controller;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use MonkeyLearn\Client;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieCollection;
use DateTime;
require(ROOT.DS. 'vendor' . DS  . 'monkeylearn' . DS . 'autoload.php');
set_time_limit(0);
error_reporting(0);

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
	public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
		$this->loadComponent('Paginator');
		/*$this->loadComponent('Paginator', [
			'paginator' => new \Cake\Datasource\SimplePaginator(),
		]);*/
		$this->loadComponent('Auth');
		
        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
    }
	public function beforeFilter(EventInterface  $event)
    {
       	parent::beforeFilter($event);
		$this->Auth->allow(['search']);
		
    }
	public function cookie($url)
	{
		$connection = ConnectionManager::get('default');
		$urlarr = array();

		if ($this->request->getCookie('urlcookie') !== null)
		{ 
			$urlarr = json_decode($this->request->getCookie('urlcookie'));
			if (!in_array($url, $urlarr))
			{
				array_push($urlarr,$url);
			}
		}
		else
			array_push($urlarr,$url);
				
		$cookie = Cookie::create('urlcookie', json_encode($urlarr), [
			'expires' => new DateTime('+30 days'),
			'http' => true,
			'secure'=> false
		]);

		$select = 'SELECT `Clicks` FROM `tblarticle` WHERE `Url` = "'.$url.'"';	
		$select_arr = $connection->execute($select)->fetchAll('assoc');
		if(count($select_arr)>0)
		{
			$clicks = $select_arr[0]['Clicks'] + 1;
			$update = 'UPDATE `tblarticle` SET `Clicks` = '.$clicks.'
						WHERE `Url` = "'.$url.'"';
			$connection->execute($update);			
		}


		$this->response = $this->response->withCookie($cookie);	
		//$urlcookie = $this->urlcookie = $urlarr;

	}
	/******************************************************admin dashboard ********************************************************/
	public function needstoapproval($filter=null)
	{
		$connection = ConnectionManager::get('default');
		if($filter=='publish')
			$status = 'published';
		else
			$status = 'prepublished';	
		$approval = 'SELECT count(Url_id) as approvecount 
					FROM `tblcrawler_queue`
					WHERE Status = "'.$status.'"';
		$approval_arr = $connection->execute($approval)->fetchAll('assoc');
		return $approval_arr[0]['approvecount'];
	}


	/****************************************************admin dashboard staging***************************************************/
	public function staging($type,$status,$limit)
	{
		$this->loadModel('Tblarticle');
		$this->loadModel('Tblusers');
		$this->loadModel('TblcrawlerQueue');
		$this->loadModel('TblrankingScore');
		$connection = ConnectionManager::get('default');
		if($type =='all')
		{
			if($status!='all')
			{
				$condition = array('TblcrawlerQueue.Status = "'.$status.'"');
				$order = array('TblcrawlerQueue.Datecreated DESC');
			}
			else
			{
				$condition = array('TblcrawlerQueue.Status NOT IN("published","prepublished")');
				$order = array( 
						'CASE
						WHEN TblcrawlerQueue.Status="submitted" THEN 1 
						WHEN TblcrawlerQueue.Status="processing" THEN 2 
						WHEN TblcrawlerQueue.Status="completed" THEN 3 
						WHEN TblcrawlerQueue.Status="error" THEN 4 END 
						ASC','TblcrawlerQueue.Datecreated DESC');				
			}			

			$urls = $this->TblcrawlerQueue->find('all', array(

			'contain' => array('Tblusers'),
			'conditions' => $condition,
			'fields' => array('TblcrawlerQueue.Url','TblcrawlerQueue.Url_id',
			'TblcrawlerQueue.Status','Tblusers.Email','TblcrawlerQueue.Datecreated'),
			'order' => $order
			));
		}
		else
		{

			if($status!='all')
			{	
				$condition = array('TblcrawlerQueue.Url LIKE "%'.$type.'%"', 
								'TblcrawlerQueue.Status="'.$status.'"',							
							);
				$order = array('TblcrawlerQueue.Datecreated DESC');				
			}
			else
			{
				$condition = array('TblcrawlerQueue.Url LIKE "%'.$type.'%"',
							'TblcrawlerQueue.Status NOT IN("published","prepublished")');
				$order = array( 
						'CASE
						WHEN TblcrawlerQueue.Status="submitted" THEN 1 
						WHEN TblcrawlerQueue.Status="processing" THEN 2 
						WHEN TblcrawlerQueue.Status="completed" THEN 3 
						WHEN TblcrawlerQueue.Status="error" THEN 4 END 
						ASC','TblcrawlerQueue.Datecreated DESC');
			}

			$urls = $this->TblcrawlerQueue->find('all', array(
			
				'contain' => array('Tblusers'),
				'join' => array('Tblarticle'=>array(
								'table'=>'tblarticle',
								'type'=>'LEFT',
								'conditions'=>array('Tblarticle.Url_id = TblcrawlerQueue.Url_id'))),
				'conditions' => $condition,
				'fields' => array('TblcrawlerQueue.Url','TblcrawlerQueue.Status','TblcrawlerQueue.Url_id',
				                  'Tblusers.Email','TblcrawlerQueue.Datecreated','Tblarticle.Article_date'),
				'order' => $order
			));		
		}
		
		$settings = [
			'page' => 1,
			'limit' => $limit,
			'maxLimit' => 100
		];	
		
		$select_arr = $this->paginate($urls,$settings);
		return $select_arr;
	}

	public function getdomain ($type=null)
	{
		$connection = ConnectionManager::get('default');
		if($type=='all')
		{
			$domain_qry = "SELECT r.Domain_name,r.Domain_id
							FROM `tblranking_score` r, `tblcrawler_queue` c ,`tblarticle` a
							WHERE r.Domain_id = a.Domain_id AND
							a.Url_id = c.Url_id AND
							c.Status  NOT IN('prepublished','published')
							GROUP BY r.Domain_id
							ORDER BY r.Domain_name  ASC";				
		}
		if($type=='published')
		{
			/*$domain_qry = "SELECT r.`Domain_id`,r.`Domain_name` FROM 
						  `tblranking_score` r, tblarticle a,tblcrawler_queue q 
						  WHERE r.`Domain_id`=a.`Domain_id` AND 
						  		a.Url_id = q.Url_id AND 
								q.Status = 'published' 
						  GROUP BY r.Domain_id
						  ORDER BY r.`Domain_name` ASC";*/
			$domain_qry = "SELECT r.`Domain_id`,r.`Domain_name`,COUNT(a.`Url_id`) as artcnt,
							SUM(a.`Clicks`) as clickcnt 
							FROM `tblranking_score` r, tblarticle a,tblcrawler_queue q 
							WHERE r.`Domain_id`=a.`Domain_id` AND 
								  a.Url_id = q.Url_id AND 
								  q.Status = 'published' 
							GROUP BY r.Domain_id
							ORDER BY r.`Domain_name` ASC";			  
			
		}
		return $domain_arr = $connection->execute($domain_qry)->fetchAll('assoc');	
	}



	/***************************************************** admin dashbaord users****************************************************/
	public function user_process($userid,$action)
	{
		$connection = ConnectionManager::get('default');
		$userid = array_diff( $userid, ['on'] );
		if($action=='mute')
			$this->do_mute($userid,'array');
		else if($action=='unmute')
			$this->do_unmute($userid,'array');
		else if($action=='delete')
			$this->delete_user($userid,'array');
	}
	public function edituser_process($userid,$action,$data=null)
	{
		$connection = ConnectionManager::get('default');
		if($action=='mute')
			$this->do_mute($userid,'string');
		else if($action=='unmute')
			$this->do_unmute($userid,'string');
		else if($action=='delete')
			$this->delete_user($userid,'string');
		else if($action=='save')
		{
			parse_str($data, $formval);
			$update_qry = "UPDATE `tblusers` SET `First_name`='".$formval[user_fname]."',
							`Last_name`='".$formval[user_lname]."',`Email`='".$formval[user_email]."' 
						   WHERE `Userid`=".$userid;
			$connection->execute($update_qry);

		}
		
	}
	public function user_editview($userid,$action)
	{
		$connection = ConnectionManager::get('default');
		$seluser_qry = "SELECT `Userid`,`First_name`,`Last_name`,`Email`,`Status` 
						FROM `tblusers` WHERE `Userid`=".$userid;
		return $user_arr = $connection->execute($seluser_qry)->fetchAll('assoc');
	}
	public function user_edit($userid,$action)
	{
		$connection = ConnectionManager::get('default');
		if($action=='mute')
			$this->do_mute($userid,'string');
		else if($action=='unmute')
			$this->do_unmute($userid,'string');
		else if($action=='delete')
			$this->delete_user($userid,'string');
	}
	public function all_users($limit)
	{
		$connection = ConnectionManager::get('default'); 
		$sel_qry = "SELECT `Userid`,`First_name`,`Last_name`,`Email`,
					`Status`,DATE(`Datecreated`) as date 
					FROM `tblusers` ORDER BY `Status` DESC LIMIT ".$limit;
		return $all_users = $connection->execute($sel_qry)->fetchAll('assoc');
	}
	public function all_users_count()
	{
		$connection = ConnectionManager::get('default'); 
		$sel_qry = "SELECT COUNT(`Userid`) as count FROM `tblusers`";
		$user_count = $connection->execute($sel_qry)->fetchAll('assoc');
		return  $user_count[0]['count'];
	}
	public function do_mute($userid,$type)
	{
		$connection = ConnectionManager::get('default'); 
		if($type=='array')
			$where="Userid IN(". implode(',', $userid).")";
		else
			$where="Userid =".$userid;
		$update_qry = "UPDATE `tblusers` SET `Status`='n' WHERE ".$where;
		$connection->execute($update_qry);
	}
	public function do_unmute($userid,$type)
	{
		$connection = ConnectionManager::get('default'); 
		if($type=='array')
			$where="Userid IN(". implode(',', $userid).")";
		else
			$where="Userid =".$userid;
		$update_qry = "UPDATE `tblusers` SET `Status`='y' WHERE ".$where;
		$connection->execute($update_qry);
		
	}
	public function delete_user($userid,$type)
	{
		$connection = ConnectionManager::get('default'); 
		if($type=='array')
		{
			$where = "`Userid` IN (". implode(',', $userid).")";
		}
		else
		{
			$where = "`Userid`=".$userid;
		}
		$delete_qry = "DELETE FROM `tblusers` WHERE ".$where;
		$connection->execute($delete_qry);
	}




	/*********************************************** admin dashbaord live and staging***************************************/
	public function edit_view($urlid)
	{
		$connection = ConnectionManager::get('default');
		//$confidence = $this::get_confidence();
		$sel_qry = "SELECT a.Article_id,a.`Article_date`,a.`Article_title`,a.`Url`,a.`Url_image`,
								a.Url_id, a.`Article_desc`, a.`Clicks`,q.Status, GROUP_CONCAT(c.Tags) AS tag,
								GROUP_CONCAT(c.Classify_id) AS Classify_id
								FROM `tblarticle` a 
								LEFT JOIN tblcrawler_queue q ON a.`Url_id` = q.Url_id 
								LEFT JOIN tblclassification c ON a.Article_id = c.Article_id 
								WHERE q.Url_id = ".$urlid." 
								GROUP BY c.Article_id";
		return $sel_art_arr = $connection->execute($sel_qry)->fetchAll('assoc');
	}
	public function common($urlids,$action)
	{
		$connection = ConnectionManager::get('default');
		if($action=='do_publish' || $action=='do_unpublish')
		{
			if($action=='do_publish')
				$status = 'published';
			else
				$status = 'prepublished';

			$this->do_publish($urlids,$status,'array');
		}
		if($action=='do_recrawl')
		{
			$this->do_recrawl($urlids,'array');
		}
		if($action=='do_crawl')
		{
			$this->do_recrawl($urlids,'string');
		}
		if($action=='do_delete')
		{
			$this->do_delete($urlids,'array');			
		}	
		if($action=='do_retag')
		{
			$update_qry = "UPDATE `tblcrawler_queue` SET `Status`='submitted',
						   Stage='monkeylearn',
						   Datecreated = '".date('Y-m-d H:i:s')."'
						   WHERE `Url_id`IN (". implode(',', $urlids).")";
			$connection->execute($update_qry);
			$delete_qry = "DELETE c FROM tblarticle a
									INNER JOIN tblcrawler_queue q ON a.Url_id = q.Url_id  
									INNER JOIN tblclassification c ON a.Article_id = c.Article_id								
									WHERE 
									a.Url_id IN(". implode(',', $urlids).")";
			$connection->execute($delete_qry);
		}
		
	}
	public function common_edit($url_id,$art_id,$action,$data)
	{
		$connection = ConnectionManager::get('default');
		if($action=='add_tag')
		{
			$tagid = $this->get_tagid();
			$confidence = $this->get_confidence();
			$data = str_replace('"', "'", $data);
			$tag = addslashes(trim($data));
			$date_created = date('Y-m-d H:i:s');
			$check_tag = "SELECT `Tags` FROM `tblclassification` WHERE 
							`Article_id` = ".$art_id." AND `Tags`='".$tag."'";
			$check_tag_arr = $connection->execute($check_tag)->fetchAll('assoc');	
			if(count($check_tag_arr)==0)
			{
				$ins_qry = "INSERT INTO `tblclassification`(`Article_id`, `Tags`,`Tag_id`, `Confidence`,`Datecreated`) 
							VALUES (".$art_id.",'".$tag."',".$tagid.",".$confidence.",'".$date_created."')";
				$connection->execute($ins_qry);
				
				$sel_classify_qry = 'SELECT `Classify_id` FROM `tblclassification` 
									WHERE `Article_id`='.$art_id.' AND `Tags`="'.$tag.'"';
				$sel_classify_arr = $connection->execute($sel_classify_qry)->fetchAll('assoc');	
				return $sel_classify_arr[0]['Classify_id'];
			}
		}
		else if($action=='remove_tag')
		{
			$del_qry = "DELETE FROM `tblclassification` 
						WHERE `Article_id`= ".$art_id." AND `Classify_id`='".$data."'";
			$connection->execute($del_qry);
		}
		else if($action=='published' || $action=='prepublished')
		{
			$this->do_publish($url_id,$action,'string');
			
		}
		else if($action=='save')
		{
			parse_str($_POST['data'], $formval);
			$article_date = $formval['edit_artdate'];
			$article_title = $formval['edit_arttitle'];
			$article_desc = $formval['edit_artdesc'];
			$article_clicks = $formval['edit_artclicks'];
			$artid = $formval['artid_live'];
			$urlid = $formval['urlid_live'];
			$update_qry = "UPDATE `tblarticle` SET `Article_title`='".addslashes($article_title)."',
												   `Article_desc`='".addslashes($article_desc)."',
												   `Article_date`='".$article_date."'
												WHERE `Article_id`=".$artid;
			$connection->execute($update_qry);
			return $urlid;
		}
		else if($action=='re_crawl')
		{
			$this->do_recrawl($url_id,'string');
		}
		else if($action=='delete')
		{
			$this->do_delete($url_id,'string');
		}
	}
	
	public function select_domain($domain)
	{
		$connection = ConnectionManager::get('default');
		if($domain=='all')
		{
			$seldomain = "SELECT SUM(a.`Clicks`) as sum_click,COUNT(a.`Url`) as article_count 
						FROM tblarticle a 
						LEFT JOIN tblranking_score r ON r.`Domain_id` = a.`Domain_id` 
						LEFT JOIN tblcrawler_queue q ON q.`Url_id`=a.`Url_id` 
						WHERE q.Status = 'published'";
		}
		else
		{
			$seldomain = "SELECT r.`Domain_name`,SUM(a.`Clicks`) as sum_click,COUNT(a.`Url`) as article_count,r.`Domain_score` 
					  FROM tblarticle a 
					  LEFT JOIN tblranking_score r ON r.`Domain_id` = a.`Domain_id` 
					  LEFT JOIN tblcrawler_queue q ON q.`Url_id`=a.`Url_id` 
					  WHERE q.Status = 'published' AND 
					  r.`Domain_name`='".$domain."'";
		}
		$domain_count_arr = $connection->execute($seldomain)->fetchAll('assoc');	
		return $domain_count_arr;
	}
	public function allarticle_domain($domain,$limit=20,$filter)
	{
		$this->loadModel('Tblarticle');
		$this->loadModel('Tblusers');
		$this->loadModel('TblcrawlerQueue');
		$this->loadModel('TblrankingScore');
		if($domain=='all')
			$domain_cond = '';
		else
			$domain_cond = 'TblrankingScore.Domain_name = "'.$domain.'" AND ';

		if($filter=='all')
			$filter_cond = 'TblcrawlerQueue.Status LIKE "%published"';
		else if($filter=='publish')
			$filter_cond = 'TblcrawlerQueue.Status = "published"';
		else if($filter=='unpublish')
			$filter_cond = 'TblcrawlerQueue.Status = "prepublished"';

		$articles = $this->Tblarticle->find('all', array(
			
			'contain' => array('TblrankingScore','TblcrawlerQueue','Tblusers'),
			'conditions' => array($domain_cond.$filter_cond),
			'fields' => array('Tblarticle.Article_date','Tblarticle.Url',
							  'Tblarticle.Article_title','Tblarticle.Clicks',
							  'TblcrawlerQueue.Status','TblcrawlerQueue.Url_id','Tblusers.Email'),
			'order' => 'Tblarticle.Clicks DESC'
		));
		$settings = ['page' => 1,
					 'limit' => $limit,
					  'maxLimit' => 100
					];
		
		
		$all_article = $this->paginate($articles,$settings);
		return $all_article;
	}

	public function queue_analysis($url=null)
	{
		$connection = ConnectionManager::get('default');
		try 
		{
			$batchid = '';
			if($url==null)
			{
				$sel_batch_qry = "SELECT `Value` FROM `tblconfig` 
								  WHERE `Key_name`='batch_size'";
				$batch_arr = $connection->execute($sel_batch_qry)->fetchAll('assoc');
				$batch_size = $batch_arr[0]['Value'];
				if($batch_size=='')
					$batch_size = 1;
				$queue_qry = "SELECT * FROM `tblcrawler_queue` WHERE `Status` 
							IN ('submitted','processing') 
							ORDER BY Datecreated DESC 
							LIMIT ".$batch_size;
				
							
			}			
			else
			{
				$queue_qry = 'SELECT * FROM `tblcrawler_queue`
							  WHERE `Url`="'.$url.'"';
			}
			
			$queue_arr = $connection->execute($queue_qry)->fetchAll('assoc');
			if(count($queue_arr)>0)
			{
				$a=1;
				$batchid_arr = array_column($queue_arr, 'Url_id');
				$batchid = implode(",", $batchid_arr);
				foreach($queue_arr as $queue)
				{
					$urlid = $queue['Url_id'];
					$userid = $queue['Userid'];
					$url = $queue['Url'];
					$stage = $queue['Stage'];
					/*$log = 'Url: '.$url;
					$fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
					fwrite($fp, "\r\n".$log);  
					fclose($fp);  */
					if($stage=='auto_extract')
					{
						/*$fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
						fwrite($fp,"\r\n".'Auto_extract start_time : '.date("Y-m-d H:i:s").';');
						fclose($fp);*/
						if ($a % 10 == 0)
						{
							sleep(5);
						}
						$ae_start = microtime(true);//ae = auto extract
						$json = $this->auto_extract($url);
						$ae_end = microtime(true);
						$ae_process_time = ($ae_end - $ae_start);
						$ae_process_time = round($ae_process_time,2);
						
						$json_start = microtime(true);
						$parser_output = $this->json_parser($json);
						$parser_output = json_decode($parser_output);
						
						if($parser_output!='')
						{
							if($parser_output->article!='')
							{
								
								if($parser_output->article->headline!='')
									$article_title = addslashes($parser_output->article->headline);
								else
									$article_title = '';
									
								if($parser_output->article->datePublished!='')
								{
									$article_date_publish = $parser_output->article->datePublished;
									$article_date =  date("Y-m-d", strtotime($article_date_publish));
								}
								else
									$article_date = '';
									
								if($parser_output->article->author!='')
									$author = $parser_output->article->author;
								else
									$author = '';
									
								if($parser_output->article->mainImage!='')
									$image_url = $parser_output->article->mainImage;
								else
									$image_url = '';
									
								if($parser_output->article->description!='')
								{
									$article_desc = addslashes($parser_output->article->description);
									if(strlen($article_desc)>100)
									{
										$article_desc_pos=strpos($article_desc, ' ', 100);
										if(!is_int($article_desc_pos))
											$article_desc_pos = strrpos($article_desc, ' ');
										$article_desc = substr($article_desc,0,$article_desc_pos); 
									}
								
								}
								else
									$article_desc = '';
									
								if($parser_output->query->domain!='')
									$domain = $parser_output->query->domain;
								else
									$domain = '';

								if($article_title!='')
								{
									$domain_id = $this->check_domain($domain);	
									
									$date_created = date('Y-m-d H:i:s');
									$ins_qry = 'INSERT INTO `tblarticle`
													(`Url_id`, `Url`, `Article_title`, 
													`Article_desc`, `Url_image`, `Article_date`, 
													`Author`, `Domain_id`,`Userid`,`Thumbs_up`, `Thumbs_down`,`Datecreated`) 
												VALUES ('.$urlid.',"'.$url.'","'.$article_title.'",
													"'.$article_desc.'","'.$image_url.'","'.$article_date.'",
													"'.$author.'","'.$domain_id.'",'.$userid.',0,0,"'.$date_created.'")';
									$connection->execute($ins_qry);
									
									$json_end = microtime(true);
									$json_process_time = ($json_end - $json_start);
									$json_process_time = round($json_process_time,2);
									$process_time = $ae_process_time.'|'.$json_process_time;
									$update_qry = "UPDATE `tblcrawler_queue` SET 
												`Status`='processing',`Stage`='monkeylearn',
												`Process_time_in_sec`='".$process_time."'
												WHERE `Url_id`=".$urlid;
									$connection->execute($update_qry);
									
									/*$fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
									fwrite($fp,'Auto_extract status:completed; Auto_extract end_time : '.date("Y-m-d H:i:s").';');
									fwrite($fp, "\r\n".'Monkeylearn start_time : '.date("Y-m-d H:i:s").';');  
									fclose($fp);*/
									$monkery_start = microtime(true);
									$tags_result = $this->monkeylearn($article_title);
									$monkery_end = microtime(true);
									$monkey_time = ($monkery_end - $monkery_start);
									$monkey_time = round($monkey_time,2);
									$dbsave = $this->dbsave($tags_result,$urlid,$monkey_time);
									
								}
								else
								{
									/*$fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
									fwrite($fp,'Auto_extract status:error; Auto_extract end_time : '.date("Y-m-d H:i:s").';');
									fclose($fp);*/
									$update_qry = 'UPDATE `tblcrawler_queue` SET 
											`Status`="error",`Status_description`="empty response from autoextract" 
											WHERE `Url_id`='.$urlid;
									$connection->execute($update_qry);
								}
								
							}
							else if($parser_output->title!='')
							{
								/*$fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
								fwrite($fp,'Auto_extract status:error; Auto_extract end_time : '.date("Y-m-d H:i:s").';');
								fclose($fp);*/
								$update_qry = 'UPDATE `tblcrawler_queue` SET 
											`Status`="error",`Status_description`="'.$parser_output->title .'" 
											WHERE `Url_id`='.$urlid;
								$connection->execute($update_qry);
							}
							else if($parser_output->error!='')
							{
								/*$fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
								fwrite($fp,'Auto_extract status:error; Auto_extract end_time : '.date("Y-m-d H:i:s").';');
								fclose($fp);*/
								$update_qry = 'UPDATE `tblcrawler_queue` SET 
											`Status`="error",`Status_description`="'.$parser_output->error .'" 
											WHERE `Url_id`='.$urlid;
								$connection->execute($update_qry);
							}
							else
							{
								/*$fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
								fwrite($fp,'Auto_extract status:error; Auto_extract end_time : '.date("Y-m-d H:i:s").';');
								fclose($fp);*/
								$update_qry = 'UPDATE `tblcrawler_queue` SET 
											`Status`="error",`Status_description`="empty response from autoextract" 
											WHERE `Url_id`='.$urlid;
								$connection->execute($update_qry);
							}
							
							
						}
						
					}
					else if($stage=='monkeylearn')
					{
						$article_qry = "SELECT `Article_title` FROM `tblarticle` WHERE `Url_id`=".$urlid;
						$article_arr = $connection->execute($article_qry)->fetchAll('assoc');
						$article_title = $article_arr[0]['Article_title'];
						if($article_title!='')
						{
							/*$fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
							fwrite($fp, "\r\n".'Monkeylearn start_time : '.date("Y-m-d H:i:s").';'); 
							fclose($fp);*/
							$monkery_start = microtime(true);
							$tags_result = $this->monkeylearn($article_title);
							$monkery_end = microtime(true);
							$monkey_time = ($monkery_end - $monkery_start);
							$monkey_time = round($monkey_time,2);
							$dbsave = $this->dbsave($tags_result,$urlid,$monkey_time);
						}
					}
					$a++;
				}

			}
		}
		catch (\Exception $e) 
		{
			$status = $e->getMessage();
		}
		return $batchid;
	}
	public function auto_extract($url)
	{
		$ch = curl_init();
		$username = Configure::read('auto_extract_user');
		$pwd = Configure::read('auto_extract_pass');
		curl_setopt($ch, CURLOPT_URL, 'https://autoextract.scrapinghub.com/v1/extract');
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$pwd");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 605000);
		curl_setopt($ch, CURLOPT_POSTFIELDS, '[{"url":"'.$url.'", "pageType": "article"}]');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		// $output contains the result
		return $output = curl_exec($ch);
		curl_close($ch);
		
	}
	public function json_parser($json)
	{
		if (!empty($json)) 
		{
			if(json_last_error() === JSON_ERROR_NONE)
			{
				$json = str_replace("\r\n","", $json);
				$json = rtrim($json,']'); 
				$json = ltrim($json,'[');
				//$json = json_decode($json);
				//echo $json->article->headline;
				return $json;
			}
		}
       
        return false;
	}
	public function monkeylearn($title)
	{
		$ml = new Client(Configure::read('monkey_clientid'));
		$data = array($title);
		$model_id = Configure::read('monkey_modelid');
		$res = $ml->classifiers->classify($model_id, $data);
		if($res->result!='')
			return $res->result;
	}
	public function dbsave($tags_result,$urlid,$monkey_time)
	{
		$connection = ConnectionManager::get('default');
		$classification = $tags_result[0]['classifications'];
		if(count($classification)>0)
		{
			
			$monkey_process_start = microtime(true);
			$sel_qry = "SELECT q.Process_time_in_sec,a.`Article_id` FROM 
						`tblarticle`a,tblcrawler_queue q WHERE 
						a.`Url_id`= q.`Url_id` AND
						a.`Url_id`=".$urlid;
			$selarr = $connection->execute($sel_qry)->fetchAll('assoc');
			$article_id = $selarr[0]['Article_id'];
			$prev_process_time = $selarr[0]['Process_time_in_sec'];
			
			for($i=0;$i<count($classification);$i++)
			{
				$tag = addslashes($classification[$i]['tag_name']);
				$tagid = $classification[$i]['tag_id'];
				$confidence_float = $classification[$i]['confidence'];
				$confidence = $confidence_float*100;
				$date_created = date('Y-m-d H:i:s');
				$ins_qry = 'INSERT INTO `tblclassification`
								(`Article_id`, `Tags`, `Tag_id`, `Confidence`,`Datecreated`) 
							VALUES ('.$article_id.',"'.$tag.'",'.$tagid.',"'.$confidence.'","'.$date_created.'")';
				$connection->execute($ins_qry);			
			}
			
			$monkey_process_end = microtime(true);
			$monkey_process_time = ($monkey_process_end - $monkey_process_start);
			$monkey_process_time = round($monkey_process_time,2);
			$new_process_time = $prev_process_time.'|'.$monkey_time.'|'.$monkey_process_time;
			/*$fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
			fwrite($fp,'Monkeylearn status:completed; Monkeylearn end_time : '.date("Y-m-d H:i:s").';');
			fclose($fp);*/
			$update_qry = "UPDATE `tblcrawler_queue` SET `Status`='completed',
						  `Stage`='completed',`Process_time_in_sec`='".$new_process_time."'
						  WHERE `Url_id`=".$urlid;
			$connection->execute($update_qry);
		}
		else if($tags_result[0]['error_detail']!='')
		{
			/*$fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
			fwrite($fp,'Monkeylearn status:error; Monkeylearn end_time : '.date("Y-m-d H:i:s").';');
			fclose($fp);*/
			$update_qry = 'UPDATE `tblcrawler_queue` SET `Status`="error",
						  `Status_description`="'.$tags_result[0]['error_detail'].'"
						  WHERE `Url_id`='.$urlid;
			$connection->execute($update_qry);
		}
		
	}
	public function check_domain($domain)
	{
		$connection = ConnectionManager::get('default');
		$sel_dist_qry = "SELECT `Domain_id` FROM `tblranking_score` 
						WHERE `Domain_name`='".$domain."'";
		$seldist_arr = $connection->execute($sel_dist_qry)->fetchAll('assoc');
		if(count($seldist_arr)>0)
		{
			$domain_id = $seldist_arr[0]['Domain_id'];
		}
		else
		{
			$sel_dscore_qry = "SELECT `Value` FROM `tblconfig` WHERE `Key_name`='domain_score'";
			$seldscore_arr = $connection->execute($sel_dscore_qry)->fetchAll('assoc');
			$dscore = $seldscore_arr[0]['Value'];
			if($dscore=='')
				$dscore = 0.5;
			$date_created = date('Y-m-d H:i:s');
			$ins_qry = "INSERT INTO `tblranking_score`(`Domain_name`, `Domain_score`,`Datecreated`) 
						VALUES ('".$domain."',".$dscore.",'".$date_created."')";
			$connection->execute($ins_qry);	
			$sel_domainid_qry = "SELECT `Domain_id` FROM `tblranking_score` 
								ORDER BY `Domain_id` DESC LIMIT 1";
			$seldomain_arr = $connection->execute($sel_domainid_qry)->fetchAll('assoc');
			$domain_id = $seldomain_arr[0]['Domain_id'];
		}
		if($domain_id=='')
			$domain_id = 1;
		return $domain_id;
	}
	public function get_confidence()
	{
		$connection = ConnectionManager::get('default');
		$config_qry = "SELECT `Value` FROM `tblconfig` WHERE `Key_name`='confidence'";
		$config_arr = $connection->execute($config_qry)->fetchAll('assoc');
		if($config_arr[0]['Value']!='')
			$confidence = $config_arr[0]['Value'];
		else
			$confidence = 0;
		return $confidence;
	}
	public function get_tagid()
	{
		$connection = ConnectionManager::get('default');
		$config_qry = "SELECT `Value` FROM `tblconfig` WHERE `Key_name`='default_tagid'";
		$config_arr = $connection->execute($config_qry)->fetchAll('assoc');
		if($config_arr[0]['Value']!='')
			$tagid = $config_arr[0]['Value'];
		else
			$tagid = 0;
		return $tagid;
	}
	public function do_recrawl($urlid,$type)
	{
		$connection = ConnectionManager::get('default');
		if($type=='array')
			$where="q.Url_id IN(". implode(',', $urlid).")";
		else
			$where="q.Url_id =".$urlid;
		
		$update_qry = "UPDATE `tblcrawler_queue` q SET q.`Status`='submitted',
						q.Stage='auto_extract',
						q.Status_description = '',
						q.Process_time_in_sec = 0.00,
						q.Datecreated = '".date('Y-m-d H:i:s')."'
						WHERE ".$where;
		$connection->execute($update_qry);
		$del_qry = "DELETE a,c,s FROM tblarticle a 
						LEFT OUTER JOIN tblcrawler_queue q ON a.Url_id = q.Url_id 
						LEFT OUTER JOIN tblclassification c ON a.Article_id = c.Article_id 
						LEFT JOIN tblsaved_article s ON s.Article_id = a.Article_id
						WHERE ".$where;
		
		$connection->execute($del_qry);
		
	}
	public function do_delete($urlid,$type)
	{
		$connection = ConnectionManager::get('default');
		if($type=='array')
			$where="q.Url_id IN(". implode(',', $urlid).")";
		else
			$where="q.Url_id =".$urlid;
			
		$delete_qry = "DELETE a,c,q,s FROM tblcrawler_queue q
			LEFT JOIN tblarticle a ON a.Url_id = q.Url_id  
			LEFT JOIN tblclassification c ON a.Article_id = c.Article_id
			LEFT JOIN tblsaved_article s ON s.Article_id = a.Article_id								
			WHERE ".$where;
		$connection->execute($delete_qry);
	}
	public function do_publish($urlid,$status,$type)
	{
		$connection = ConnectionManager::get('default');
		
		if($type=='array')
			$where="Url_id IN(". implode(',', $urlid).")";
		else
			$where="Url_id =".$urlid;

			$update_qry = "UPDATE tblcrawler_queue SET `Status` = '".$status."'
								WHERE ".$where;
		
		$connection->execute($update_qry);
	}
	
	/*****************user dasboard saved*************************/
	public function savedarticle($userid,$type,$search,$limit)
	{
		
		$connection = ConnectionManager::get('default');
		$this->loadModel('Tblarticle');
		$this->loadModel('Tblusers');
		$this->loadModel('Tblclassification');
		$this->loadModel('TblsavedArticle');

		$confidence = $this->get_confidence();
		if($type=='all')
			$condition = array("TblsavedArticle.`Userid`=".$userid);
		else if($type=='week')
			$condition = array("TblsavedArticle.`Userid`= ".$userid,
							   "DATE(TblsavedArticle.`Datecreated`) > DATE_SUB(NOW(), INTERVAL 1 WEEK)");
		else if($type=='search')	
			$condition = array('OR'=>
									array("Tblarticle.`Url` LIKE '%".$search."%'",
										  "Tblarticle.`Article_title` LIKE '%".$search."%'",
										  "Tblarticle.`Article_desc` LIKE '%".$search."%'") ,
										  array("TblsavedArticle.`Userid`=".$userid)
								);

			
		$saved_articles = $this->Tblarticle->find('all', array(
			'join' => array(
						array(
							'table'=>'tblsaved_article',
							'alias' => 'TblsavedArticle',
							'type'=>'INNER',
							'conditions'=>array('TblsavedArticle.Article_id = Tblarticle.Article_id')
						),
						array(
							'table'=>'tblclassification',
							'alias' => 'Tblclassification',
							'type'=>'LEFT',
							'conditions'=>array('Tblclassification.Article_id = Tblarticle.Article_id',
												'Tblclassification.Confidence>='.$confidence)
						)),
			'conditions' => $condition,
			'fields' => array('Article_id'=>'TblsavedArticle.Article_id',
							  'Tblarticle.Article_desc',
							  'Tblarticle.Article_date',
							  'Tblarticle.Url',
							  'tag' => 'GROUP_CONCAT(Tblclassification.Tags)'),
			'order' => 'TblsavedArticle.Datecreated DESC',
			'group' => 'Tblarticle.Article_id'
			
		));
		
		$settings = ['page' => $page,
					 'limit' => $limit,
		    		 'maxLimit' => 100
					];
		$sel_arr = $this->paginate($saved_articles,$settings);

		return $sel_arr;

	}

	/***  function for index page */
	public function searcharticle()
	{
		try 
		{
			$this->loadModel('Tblarticle');
			$this->loadModel('Tblusers');
			$this->loadModel('TblcrawlerQueue');
			$this->loadModel('TblrankingScore');
			$this->loadModel('Tblclassification');
			$connection = ConnectionManager::get('default');
			$confidence = $this->get_confidence();
			$filter = $_SESSION['filter'];
			$tag = $_SESSION['tag'];
			if($_SESSION['sorting']!='')
				$orderby = '(TblrankingScore.Domain_score*Clicks) DESC';
			else
				$orderby = 'Tblarticle.Article_date DESC';
			
			if($tag!='')
			{
				if(strpos($tag,"'")>0)
					$tag = stripslashes($tag);
				$tag_arr = explode(",",$tag);
				$tag_arr = array_unique($tag_arr);
				if($_SESSION['match_tag']!='')
				{
					if(strpos($_SESSION['match_tag'],"'")>0)
						$matchtag = stripslashes($_SESSION['match_tag']);
					else
						$matchtag = $_SESSION['match_tag'];
					
					if (!in_array($matchtag, $tag_arr))
					{
						$_SESSION['match_tag'] = 'false';
						$_SESSION['index_search'] = '';
						
					}
				}
				$alltag = implode('","',$tag_arr);
				$count = count($tag_arr);
				if($filter!='' && $filter!='all')
				{
					
					$artid_qry ='SELECT a.Article_id FROM tblarticle a LEFT JOIN tblclassification c 
											ON (c.Article_id = a.Article_id AND c.Confidence>='.$confidence.') 
											INNER JOIN tblranking_score r ON r.Domain_id = (a.Domain_id) 
											INNER JOIN tblcrawler_queue q ON q.Url_id = (a.Url_id) 
											WHERE MATCH (a.`Article_title`, a.`Article_desc`) AGAINST ("'.$filter.'") AND 
											c.Tags IN ("'.$alltag.'") AND q.Status = "published" 
											GROUP BY c.Article_id HAVING COUNT(c.Article_id) ='.$count;
					$fields_select = array('Article_id','Article_date','Article_title',
											  'Url_image','Article_desc','url'=>'TblcrawlerQueue.Url',
											  'domain'=>'TblrankingScore.Domain_name',
											  'tag'=>'GROUP_CONCAT(Tblclassification.Tags)',
											  'relevance'=>'MATCH (`Article_title`, `Article_desc`) AGAINST ("'.$filter.'")',
											  'title_relevance'=>'MATCH (`Article_title`) AGAINST ("'.$filter.'")');
					$orderby = 'title_relevance+relevance DESC,'.$orderby;
					$artidarr = $connection->execute($artid_qry)->fetchAll('assoc');
				
				}
				
				else
				{
					if($_SESSION['seo']!='')
					{
			
						/*$artid_qry ="SELECT a.Article_id,c.Tags FROM `tblclassification` c,tblarticle a, tblcrawler_queue q
										WHERE c.Article_id = a.Article_id AND
										a.Url_id = q.Url_id AND
										q.Status = 'published' AND 
										TRIM(TRAILING '-' FROM LOWER(REPLACE(REPLACE(REGEXP_REPLACE(REGEXP_REPLACE(c.`Tags`,' ','-'), '[^a-zA-Z0-9\.]','-'),'--','-'),'--','-')))
										IN ('".$alltag."') AND 
										c.`Confidence`>=".$confidence." GROUP BY c.Article_id HAVING COUNT(c.Article_id) =".$count;*/
						$artid_qry ="SELECT a.Article_id,c.Tags FROM `tblclassification` c,tblarticle a, tblcrawler_queue q
									WHERE c.Article_id = a.Article_id AND
									a.Url_id = q.Url_id AND
									q.Status = 'published' AND
									TRIM(TRAILING '-' FROM LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(c.`Tags`,' ','-'), '&','-'),'?','-'),'--','-'),'--','-')))
									IN ('".$alltag."') AND 
									c.`Confidence`>=".$confidence." GROUP BY c.Article_id HAVING COUNT(c.Article_id) =".$count;
						
						$artidarr = $connection->execute($artid_qry)->fetchAll('assoc');
						if(count($artidarr)>0)
						{
							$seo_tag = $artidarr[0]['Tags'];
							$_SESSION['tag'] = $seo_tag;
						}
					}
					else
					{
						$artid_qry ='SELECT c.Article_id FROM `tblclassification` c,tblarticle a,tblcrawler_queue q
									WHERE  c.Article_id = a.Article_id AND
									a.Url_id = q.Url_id AND
									q.Status = "published" AND
									c.Tags IN ("'.$alltag.'") AND 
									c.`Confidence`>='.$confidence.' GROUP BY c.Article_id HAVING COUNT(c.Article_id) ='.$count;
						$artidarr = $connection->execute($artid_qry)->fetchAll('assoc');
					}
					$fields_select = array('Article_id','Article_date','Article_title',
							'Url_image','Article_desc','url'=>'TblcrawlerQueue.Url','domain'=>'TblrankingScore.Domain_name',
							'tag'=>'GROUP_CONCAT(Tblclassification.Tags)');
				}
				if(count($artidarr)>0)
				{
					$artids =  implode(', ', array_column($artidarr, 'Article_id'));
					$articles = $this->Tblarticle->find('all', array(
							
						'contain' => array('TblrankingScore','TblcrawlerQueue'),
						'join' => array('Tblclassification'=>array(
														'table'=>'tblclassification',
														'type'=>'INNER',
														'conditions'=>array('Tblclassification.Article_id = Tblarticle.Article_id',
																			'Tblclassification.Confidence>='.$confidence))),
														
						'fields' => $fields_select,
						'conditions' => array('Tblarticle.Article_id IN('.$artids.')',array('TblcrawlerQueue.Status = "published"')),
						'order' => $orderby,
						'group' => 'Tblclassification.Article_id',
					));
					
					/*$tag_query_alpha = 'Select DISTINCT (`Tags`) from tblclassification where 
									Article_id IN('.$artids.') AND
									`Confidence`>='.$confidence.' AND Tags NOT IN ("'.$alltag.'")
									ORDER BY Tags ASC';
										
					 $tag_query_top = 'SELECT DISTINCT (`Tags`),COUNT(`Tags`) AS cnt 
									  FROM tblclassification where Article_id IN('.$artids.') AND
									  `Confidence`>='.$confidence.' AND Tags NOT IN ("'.$alltag.'")
										GROUP BY `Tags` HAVING (cnt>0) 
									ORDER BY cnt DESC';*/
				}
				
				$tag_query_top = 'SELECT c.`Tags` as Tags,COUNT(c.`Tags`) AS cnt 
									FROM `tblclassification` c,tblarticle a,tblcrawler_queue q
									WHERE  a.Article_id=c.`Article_id` AND
										   q.Url_id=a.Url_id AND
										   c.Confidence>='.$confidence.' AND 
									q.Status="published" AND Tags NOT IN ("'.$alltag.'") 
									GROUP BY c.`Tags` HAVING (cnt>0) 
									ORDER BY cnt DESC';
								
				$tag_query_alpha = 'SELECT DISTINCT (`Tags`) as Tags FROM `tblclassification` c,
								tblarticle a,tblcrawler_queue q
								WHERE  a.Article_id=c.`Article_id` AND
								   q.Url_id=a.Url_id AND Tags NOT IN ("'.$alltag.'") AND
								   c.Confidence>='.$confidence.' AND 
								q.Status="published" 
								ORDER BY Tags ASC';
				
			}
			else
			{
				if($filter=='all')
				{

												
					$articles = $this->Tblarticle->find('all', array(
						
						'contain' => array('TblrankingScore','TblcrawlerQueue'),
						'join' => array('Tblclassification'=>array(
														'table'=>'tblclassification',
														'type'=>'LEFT',
														'conditions'=>array('Tblclassification.Article_id = Tblarticle.Article_id',
																			'Tblclassification.Confidence>='.$confidence))),
						'fields' => array('Article_id','Article_date','Article_title',
										'Url_image','Article_desc','url'=>'TblcrawlerQueue.Url','domain'=>'TblrankingScore.Domain_name',
										'tag'=>'GROUP_CONCAT(Tblclassification.Tags)'),
						'conditions' => array('TblcrawlerQueue.Status = "published"'),
						'order' => $orderby,
						'group' => 'Tblarticle.Article_id',
						
					));
					
						
					/*$tag_query_top = "SELECT c.`Tags` as Tags,COUNT(c.`Tags`) AS cnt 
									FROM `tblclassification` c,tblarticle a,tblcrawler_queue q
									WHERE  a.Article_id=c.`Article_id` AND
										   q.Url_id=a.Url_id AND
										   c.Confidence>=".$confidence." AND 
									q.Status='published' GROUP BY c.`Tags` HAVING (cnt>0) 
									ORDER BY cnt DESC";
								
					$tag_query_alpha = "SELECT DISTINCT (`Tags`) as Tags FROM `tblclassification` c,
										tblarticle a,tblcrawler_queue q
										WHERE  a.Article_id=c.`Article_id` AND
										   q.Url_id=a.Url_id AND
										   c.Confidence>=".$confidence." AND 
										q.Status='published'
										ORDER BY Tags ASC";*/
					

				}
				else
				{
								
					$neworderby = 'title_relevance+relevance DESC,'.$orderby;
					$articles = $this->Tblarticle->find('all', array(
						
						'contain' => array('TblrankingScore','TblcrawlerQueue'),
						'join' => array('Tblclassification'=>array(
														'table'=>'tblclassification',
														'type'=>'LEFT',
														'conditions'=>array('Tblclassification.Article_id = Tblarticle.Article_id',
																			'Tblclassification.Confidence>='.$confidence))),
						'fields' => array('Article_id','Article_date','Article_title',
										'Url_image','Article_desc','url'=>'TblcrawlerQueue.Url',
										'relevance'=>'MATCH (`Article_title`, `Article_desc`) AGAINST ("'.$filter.'")',
										'title_relevance'=>'MATCH (`Article_title`) AGAINST ("'.$filter.'")',
										'domain'=>'TblrankingScore.Domain_name',
										'tag'=>'GROUP_CONCAT(Tblclassification.Tags)'),
						/*'conditions' =>array('OR'=>
												array('Tblarticle.Article_desc REGEXP ("'.$filter.'")',
														'Tblarticle.Article_title REGEXP ("'.$filter.'")')),*/
						'conditions' =>array('MATCH (`Article_title`, `Article_desc`) AGAINST ("'.$filter.'")',array('TblcrawlerQueue.Status = "published"')),
						'order' => $neworderby,
						'group' => 'Tblarticle.Article_id',
						
					));
					
					
					/*$tag_query_alpha = 'SELECT DISTINCT(Tblclassification.Tags) FROM 
									tblarticle Tblarticle LEFT OUTER JOIN tblclassification Tblclassification ON 
									(Tblclassification.Article_id = Tblarticle.Article_id AND Tblclassification.Confidence>='.$confidence.') 
									INNER JOIN tblranking_score TblrankingScore ON TblrankingScore.Domain_id = (Tblarticle.Domain_id) 
									INNER JOIN tblcrawler_queue TblcrawlerQueue ON TblcrawlerQueue.Url_id = (Tblarticle.Url_id) 
									WHERE (MATCH (`Article_title`, `Article_desc`) AGAINST ("'.$filter.'") AND 
									TblcrawlerQueue.Status = "published")
									ORDER BY `Tags` ASC';
									
					$tag_query_top = 'SELECT DISTINCT(Tblclassification.Tags),COUNT(Tblclassification.`Tags`) AS cnt FROM 
									tblarticle Tblarticle LEFT OUTER JOIN tblclassification Tblclassification ON 
									(Tblclassification.Article_id = Tblarticle.Article_id AND Tblclassification.Confidence>='.$confidence.') 
									INNER JOIN tblranking_score TblrankingScore ON TblrankingScore.Domain_id = (Tblarticle.Domain_id) 
									INNER JOIN tblcrawler_queue TblcrawlerQueue ON TblcrawlerQueue.Url_id = (Tblarticle.Url_id) 
									WHERE (MATCH (`Article_title`, `Article_desc`) AGAINST ("'.$filter.'") AND 
									TblcrawlerQueue.Status = "published") GROUP BY Tblclassification.`Tags` HAVING (cnt>0) 
									ORDER BY cnt DESC';*/
									
				}
				
				$tag_query_top = "SELECT c.`Tags` as Tags,COUNT(c.`Tags`) AS cnt 
									FROM `tblclassification` c,tblarticle a,tblcrawler_queue q
									WHERE  a.Article_id=c.`Article_id` AND
										   q.Url_id=a.Url_id AND
										   c.Confidence>=".$confidence." AND 
									q.Status='published' GROUP BY c.`Tags` HAVING (cnt>0) 
									ORDER BY cnt DESC";
								
				$tag_query_alpha = "SELECT DISTINCT (`Tags`) as Tags FROM `tblclassification` c,
									tblarticle a,tblcrawler_queue q
									WHERE  a.Article_id=c.`Article_id` AND
									   q.Url_id=a.Url_id AND
									   c.Confidence>=".$confidence." AND 
									q.Status='published'
									ORDER BY Tags ASC";
									
			}
			
			
			if($articles!='')
			{
				
				$limit = 10;
				
				$parameters = $this->request->getAttribute('params');

				$page_url = $parameters['param1'];

				if(is_numeric($page_url))
					$page = $page_url;
				else
					$page = 1;
				
				$settings = ['page' => $page,
							'limit' => $limit,
							];
				$responsearr[0] = $this->paginate($articles,$settings);
			}
			else
				$responsearr[0] = array();
			
			if($tag_query_alpha!='')
			{
				$responsearr[1] = $connection->execute($tag_query_alpha)->fetchAll('assoc');
				$responsearr[2] = $connection->execute($tag_query_top)->fetchAll('assoc');
			}
			else
			{
				$responsearr[1] =''	;
				$responsearr[2] = '';
			}
		}
		catch (\Exception $e) 
		{
			$status = $e->getMessage();
			$responsearr[3] = $status;
		}
		return $responsearr;
		
	}
	public function save_article()
	{
		$connection = ConnectionManager::get('default');
		
		$selcnt_qry = "SELECT `Id` FROM `tblsaved_article` 
					   WHERE `Userid`= ".$_SESSION['userid']." AND 
					   `Article_id`=".$_SESSION['artid'];
		$count_arr = $connection->execute($selcnt_qry)->fetchAll('assoc');
		if(count($count_arr)==0)
		{
			$date_created = date('Y-m-d H:i:s');
			$insert_qry = "INSERT INTO `tblsaved_article`(`Userid`, `Article_id`,`Datecreated`) 
							VALUES (".$_SESSION['userid'].",".$_SESSION['artid'].",'".$date_created."')";
			$connection->execute($insert_qry);
		}
	}
	public function datalist_tag()
	{
		$connection = ConnectionManager::get('default');
		$tag_distict = "SELECT DISTINCT(`Tags`) AS Tags FROM `tblclassification` c, 
						tblarticle a,tblcrawler_queue q WHERE c.`Article_id` = a.`Article_id` AND 
						a.Url_id=q.Url_id AND q.Status='published' ORDER BY Tags ASC";
		$tag_arr = $connection->execute($tag_distict)->fetchAll('assoc');
		return $tag_arr;
	}
}
