<?php
namespace App\Controller;use Cake\Routing\Router;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\ORM\Table;
use Cake\ORM\Locator\LocatorAwareTrait;
use MonkeyLearn\Client;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Cookie\CookieCollection;
require(ROOT.DS. 'vendor' . DS  . 'monkeylearn' . DS . 'autoload.php');
require(ROOT.DS. 'vendor' . DS  . 'smtp' . DS . 'class.phpmailer.php');
require(ROOT.DS. 'vendor' . DS  . 'smtp' . DS . 'class.smtp.php');
use PHPMailer;
set_time_limit(0);
error_reporting(0);
session_start();
class UsersController extends AppController
{ 
	var $helpers = array('Html', 'Form', 'Csv', 'Js', 'Paginator');
	public function beforeFilter(EventInterface  $event)
    {
       	parent::beforeFilter($event);
		   $this->Auth->allow(['process','search','config','urlprocess','signout','adminDashboard','adminDashboardLive','adminDashboardUsers',
		   'adminDashboardStaging','adminSetting','paginationTest','userDashboard','userDashboardSaved','index','signin','signup','about','contact','dbchange','notFound']);
	
    }
	public function signup()
	{
		$this->autoLayout = false;
		$connection = ConnectionManager::get('default');
		if($this->request->is('post'))
		{
			if($_POST['first_name']!='')
			{
				$sel_qry = "SELECT COUNT(`Email`) as count FROM `tblusers` 
							WHERE `Email`='".$_POST['email']."'";
				$count_arr = $connection->execute($sel_qry)->fetchAll('assoc');
				$count = $count_arr[0]['count'];
				if($count==0)
				{
					$randnum = str_pad(rand(0,999), 5, "9", STR_PAD_LEFT);
					$password = $_POST['password'];
					$hash_password = sha1($password);
					if($_POST['agree']=='on')
						$agreement = 'y';
					else
						$agreement = 'n';
					
					$date_created = date('Y-m-d H:i:s');
					$ins_qry = "INSERT INTO `tblusers`(`First_name`, `Last_name`, `Email`,
													`Password`, `Role`,`Agreement`, 
													`Status`, `Verification_code`,
													`Datecreated`) 
										VALUES ('".$_POST['first_name']."','".$_POST['last_name']."','".$_POST['email']."',
										'".$hash_password."','user','".$agreement."','p','".$randnum."',
										'".$date_created."')";
					$connection->execute($ins_qry);
					$fromaddress = 'admin@retirementcasa.com';
					$toaddress = $_POST['email'];
					$subject = 'Retirementcasa verification code';
					$message = 'To activate your account enter this verification code in that site :'.$randnum;
					$FromName = 'Admin Retirementcasa';
					$this->sendmail($fromaddress,$FromName,$toaddress,$subject,$message);
					
				}
				else
				{
					$_POST['email'] = '';
					$this->set('error','User already exist');
				}
			}
			else if($_POST['verify_code']!='')
			{
				$sel_qry = "SELECT `Verification_code` FROM `tblusers` 
							WHERE `Email`='".$_POST['prev_email']."'";
				$verify_arr = $connection->execute($sel_qry)->fetchAll('assoc');
				$verify_code = $verify_arr[0]['Verification_code'];
				if($_POST['verify_code']==$verify_code)
				{
					$update_qry = "UPDATE `tblusers` SET `Status`='y' 
								WHERE `Email`='".$_POST['prev_email']."'";
					$connection->execute($update_qry);
					$this->redirect(['action' => 'signin']);
				}
				else
					$this->set('error','Verification code not match');
			}
		}
	}
	public function signin()
	{
		$connection = ConnectionManager::get('default');
		if($this->request->is('post'))
		{
			if($_POST['email']!='')
			{
				$email = $_POST['email'];
				$password = sha1($_POST['password']);
				$sel_qry = "SELECT `Userid`,`First_name`,`Last_name`,`Status`,`Role`,`Verification_code`
							FROM `tblusers` 
							WHERE Email='".$email."' AND 
							Password='".$password."'";
				$sel_arr = $connection->execute($sel_qry)->fetchAll('assoc');
				if(count($sel_arr)>0)
				{
					$_SESSION['username'] = $sel_arr[0]['First_name'].' '.$sel_arr[0]['Last_name'];
					$_SESSION['userid'] = $sel_arr[0]['Userid'];
					$_SESSION['role'] = $sel_arr[0]['Role'];
					$status = $sel_arr[0]['Status'];
					if($status=='p')
					{
						$verify_code = $sel_arr[0]['Verification_code'];
						$_SESSION['verify_code'] = $verify_code;
						$this->set('verify_code','yes');
					}
					else if($status=='y')
					{												if($sel_arr[0]['Role']=='admin')
							$this->redirect(['controller' => 'users','action' => 'adminDashboard']);
						else
							$this->redirect(['controller' => 'users','action' => 'userDashboard']);
					}
					else if($status=='n')
					{
						$this->set('error','Not a active user');
					}
				}
				else
					$this->set('error','Invalid username or password');

			}
			else if($_POST['verify_code']!='')
			{
				if($_SESSION['verify_code']==$_POST['verify_code'])
				{
					$update_qry = "UPDATE `tblusers` SET `Status`='y' 
									WHERE `Userid`=".$_SESSION['userid'];
					$connection->execute($update_qry);
					$_SESSION['verify_code'] = '';
					$this->redirect(['action' => 'userDashboard']);
				}
				else
				{
					$this->set('verify_code','yes');
					$this->set('error','Verification code not match');
				}
			}
		}
	}
	public function signout()
	{
		$_SESSION['username'] = '';
		$_SESSION['userid'] = '';
		$_SESSION['role'] = '';
		$this->redirect(['action' => 'signin']);
	}		public function notFound()		{				/*$response = $this->response->withStatus(404);		return $response;*/	}
	public function index()
	{
		$this->autoLayout = false;				$connection = ConnectionManager::get('default');				$baseUrl = Router::url('/', true);				$this->set('actual_link',$baseUrl);
		if($this->request->is('post'))
		{
			if($this->request->is('ajax'))
			{
				if($_POST['action']=='cookie')
				{
					if ($this->request->getCookie('urlcookie') !== null)
					{ 					
						$json = json_decode($this->request->getCookie('urlcookie'));
						if(in_array($_POST['url'],$json)==false)					
							$cookie = parent::cookie($_POST['url']);
					}
					else
						$cookie = parent::cookie($_POST['url']);				
				}
				if($_POST['action']=='save_article')
				{
					if($_SESSION['userid']=='')
					{
						$_SESSION['artid'] = $_POST['artid'];
						echo $action = 'signin';

					}
					else
					{
						
						$_SESSION['artid'] = $_POST['artid'];
						parent::save_article();
						echo 'saved';
					}
					exit;
				}
				else
				{
					if($_POST['action']=='article_search')
					{						$_SESSION['seo'] = '';												if($_POST['tag_select']!='')													{														$_SESSION['index_search'] = $_POST['tag_select'];														$_SESSION['tag'] = $_POST['tag_select'];														$_SESSION['match_tag'] = $_POST['tag_select'];														$_SESSION['filter'] = 'all';														$responsearr = parent::searcharticle();													}
						if($_POST['index_search']!='')
						{														$_SESSION['index_search'] = $_POST['index_search'];														$str = $_POST['index_search'];														$check_tag_qry = "SELECT DISTINCT(Tags) FROM `tblclassification` 											  WHERE LOWER(`Tags`) = '".strtolower(addslashes($str))."'";														$check_tag_arr = $connection->execute($check_tag_qry)->fetchAll('assoc');																					$check_tag = $check_tag_arr[0]['Tags'];														if($check_tag!='')															{								$_SESSION['tag'] = $check_tag;																$_SESSION['filter'] = 'all';																$_SESSION['match_tag'] = $check_tag;															$responsearr = parent::searcharticle();							}                            							else															{							 
								$str_arr = explode(" ",$str);
								$arr = '';
								foreach($str_arr as $val)
								{
									if(strlen($val)>3)
									{
										$val = strtolower($val);
										$val = preg_replace('/[^A-Za-z0-9\-]/', '', $val);
										$wh_arr = array('','will','what','where','shall','when','could','should','would','your','whats');
										$space = ' ';
										if(array_search($val,$wh_arr)=='')										{
											$arr.=$space.trim($val).$space.'|';										}
									}
									
								}
								$arr_val = rtrim($arr,'|');
								$_SESSION['filter'] = $arr_val;
								$_SESSION['sorting'] = '';
								$_SESSION['index_search'] = $_POST['index_search'];
								$_SESSION['tag'] = ''; 																$_SESSION['match_tag'] = '';
								$responsearr = parent::searcharticle();
							}
						}
					}
					if($_POST['action']=='tag_filter')
					{												$_SESSION['sorting'] = '';
						if($_POST['tag']!='')						{
							$_SESSION['tag'] = $_POST['tag'];																													}														else													{
							$_SESSION['tag'] = '';							if($_SESSION['filter']=='all')							{								$_SESSION['index_search'] ='';															}														}
						$_SESSION['seo'] = '';												$responsearr = parent::searcharticle();
					}
					if($_POST['action']=='sorting_index')
					{						if($_POST['sortmode']=='most_popular')													{							$_SESSION['sorting'] = $_POST['sortmode'];						}						else						{							$_SESSION['sorting'] = '';						}
						$responsearr = parent::searcharticle();
					}
					$this->set('article_arr',$responsearr[0]);
					$this->set('tag_arr_alpha',$responsearr[1]);										$this->set('tag_arr_top',$responsearr[2]);										return $this->render('index_filter');
					exit;
				}
				
			}
		}
		else
		{			
			$parameters = $this->request->getAttribute('params');
			$page_url = $parameters['param1'];						
			if(is_numeric($page_url))
			{				
				$_SESSION['seo'] = '';								if($_SESSION['tag'] == '')				{										if(($parameters['param']!='') && (is_numeric($page_url)))					{						$_SESSION['tag'] = $parameters['param'];					}									}
				$responsearr = parent::searcharticle();
			}
			else if($page_url=='')
			{								$_SESSION['sorting'] = '';
				if($parameters['pass'][0]=='')
				{										if($_SESSION['redirect_header']!='')											{						$_SESSION['redirect_header'] = '';												$_SESSION['seo'] = '';																											}															else											{
						$_SESSION['filter'] = 'all';
						$_SESSION['index_search'] = '';
						$_SESSION['tag'] = '';
						$_SESSION['seo'] = '';
					}
					$responsearr = parent::searcharticle();
				}
			}						else			{								if(($parameters['param']!='') && (!is_numeric($page_url)))				{					$this->redirect(['controller' => 'users','action' => 'notFound']);									}				else				{													$_SESSION['filter'] = 'all';					$_SESSION['sorting'] = '';					$_SESSION['index_search'] = '';					$_SESSION['tag'] =$parameters['pass'][0];					$_SESSION['seo'] = $parameters['pass'][0];					$_SESSION['show_tag'] = '';					$_SESSION['tag_count'] = 0;					$responsearr = parent::searcharticle();					$checkcount = count($responsearr[0]);					if($checkcount==0)						$this->redirect(['controller' => 'users','action' => 'notFound']);									}							}			
			$this->set('article_arr',$responsearr[0]);
			$this->set('tag_arr_alpha',$responsearr[1]);								$this->set('tag_arr_top',$responsearr[2]);												if($responsearr[3]!='')			{				$this->redirect(['controller' => 'users','action' => 'notFound']);													}			
            $datalist_tags = parent::datalist_tag();						$this->set('datalist_tags',$datalist_tags);
		}

	}
	public function adminDashboard()
	{
		if($_SESSION['userid']=='')
		{
			$this->redirect(['action' => 'signin']);
		}
		else
		{
			if ($this->request->getCookie('urlcookie') !== null) 
			{
				$json = json_decode($this->request->getCookie('urlcookie'));
				
			}
			
			
			$connection = ConnectionManager::get('default');
			$this->autoLayout = false;
			if($this->request->is('ajax'))
			{
				if($_POST['action']=='click_approval')
				{
					$_SESSION['stage']= 'click_approval';
					exit;
				}
			}
			else
			{
				//count of approval
				$approval = parent::needstoapproval();
				$this->set('approvalcount',$approval);

				//count domain,article
				$totalcount = "SELECT count(a.Url_id) as articlecount,
							count(DISTINCT(a.Domain_id)) as 
							domaincount FROM `tblcrawler_queue` c,`tblarticle` a 
							WHERE c.Status = 'published' and a.Url_id= c.Url_id";
				$totalcount_arr = $connection->execute($totalcount)->fetchAll('assoc');				
				$this->set('domaincount',$totalcount_arr[0]['domaincount']);
				$this->set('articlecount',$totalcount_arr[0]['articlecount']);

				//count of active users
				$user_count_qry ="SELECT COUNT(First_name) as count FROM `tblusers`
								WHERE Status='y' ORDER BY Datecreated ASC";
				$user_count = $connection->execute($user_count_qry)->fetchAll('assoc');
				$this->set('user_count',$user_count[0]['count']);

				/* users for current week */
				$sel_user_qry = "SELECT DATE(`Datecreated`) AS date,`First_name`,`Last_name`,`Email`
								FROM tblusers WHERE Role!='admin' AND
								DATE(`Datecreated`) > DATE_SUB(NOW(), INTERVAL 1 WEEK) 
								ORDER BY `Datecreated` DESC";
				$userarr = $connection->execute($sel_user_qry)->fetchAll('assoc');
				$this->set('userarr',$userarr);

				//tags
				$confidence = parent::get_confidence();
				$tags = "SELECT `Tags`,COUNT(`Tags`) AS cnt 
						FROM `tblclassification` 
						WHERE  Confidence>=".$confidence."
						GROUP BY `Tags` HAVING (cnt>1) 
						ORDER BY cnt DESC LIMIT 15";
				$tags_arr = $connection->execute($tags)->fetchAll('assoc');
				$this->set('tags_arr',$tags_arr);	
			}
		}
	}
	public function adminDashboardStaging()
	{
		if($_SESSION['userid']=='')
		{
			$this->redirect(['action' => 'signin']);
		}
		else
		{
			$connection = ConnectionManager::get('default');
			$this->autoLayout = false;
			$this->set('actionname', $this->request->getParam('action'));
		
			if($this->request->is('ajax'))
			{
				
				if($_POST['action']=='error_view')
				{
					$urlid = $_POST['url_id'];
					$sel_qry = "SELECT Stage,Status_description 
								FROM `tblcrawler_queue` WHERE Url_id=".$urlid;
					$select_arr = $connection->execute($sel_qry)->fetchAll('assoc');
					$this->set('error_arr',$select_arr);			
					$this->set('render','error');
					$this->render('admin_dashboard_staging_filter');
					exit;
				}
				if($_POST['search']!='')
					$search = $_POST['search'];
				else
					$search = 'all';
				$stagingarr = array_diff( $_POST['stagingarr'], ['on'] );
				$in_arr = array();
				if($_POST['action']=='common')
				{
					$processtype = $_POST['processtype'];
					if($processtype=='do_recrawl')
						$in_arr = ['submitted','completed','error'];
					if($processtype =='do_publish' || $processtype =='do_unpublish')
						$in_arr = ['completed'];
					if($processtype=='do_delete')
						$in_arr = ['completed','submitted','error','processing'];

					$select_qry = "SELECT Url_id FROM `tblcrawler_queue` WHERE Url_id IN(". implode(',', $stagingarr).")
								and Status IN('".implode("','", $in_arr)."')";
					$select_arr = $connection->execute($select_qry)->fetchAll('assoc');
					$stagingcount = count($stagingarr);
					$urlid_arr = array_column($select_arr,'Url_id');
					$urlcount = count($urlid_arr);
					$totalcount = $stagingcount-$urlcount ;

					if($urlcount>0)
						$staging = parent::common($urlid_arr,$_POST['processtype']);

					$select_arr = parent::staging($search,$_POST['status'],$_POST['limit']);
					$_SESSION['stage_type']=$search;
					$_SESSION['stage_status']=$_POST['status'];
					$_SESSION['stage_limit'] = $_POST['limit'];
					$this->set('status',$_POST['status']);
					$this->set('limit',$_POST['limit']);

					//count of approval
					$approval = parent::needstoapproval();
					$this->set('staging',$approval);

				}
				if($_POST['action']=='do_crawl')
				{
					$staging = parent::common($_POST['id'],$_POST['action']);
					$select_arr = parent::staging($search,$_POST['status'],$_POST['limit']);
					$_SESSION['stage_type']=$search;
					$_SESSION['stage_status']=$_POST['status'];
					$_SESSION['stage_limit'] = $_POST['limit'];
					$this->set('status',$_POST['status']);
					$this->set('limit',$_POST['limit']);
				}
				if($_POST['action']=='search')
				{
					$select_arr = parent::staging($search,'all',$_POST['limit']);
					$_SESSION['stage_type']=$search;
					$_SESSION['stage_status']='all';
					$_SESSION['stage_limit'] = $_POST['limit'];
					$this->set('status','all');
					$this->set('limit',$_POST['limit']);
				}
				if($_POST['action']=='status')
				{
					$select_arr = parent::staging($search,$_POST['status'],$_POST['limit']);
					$_SESSION['stage_type']=$search;
					$_SESSION['stage_status']=$_POST['status'];
					$_SESSION['stage_limit'] = $_POST['limit'];
					$this->set('status',$_POST['status']);
					$this->set('limit',$_POST['limit']);

				}
				if($_POST['action']=='addurl')
				{
					$explode = explode("\n",trim($_POST['url']));
					$urlcount = count($explode);
					$batch_qry = "SELECT Batchid FROM tblcrawler_queue ORDER BY Batchid DESC LIMIT 1";
					$batch = $connection->execute($batch_qry)->fetchAll('assoc');
					$batchid = $batch[0]['Batchid']+1;
					$date_created = date('Y-m-d H:i:s');
					
					if($_SESSION['userid']!='')
						$userid = $_SESSION['userid'];
					else
					{
						$sel_user_qry = "SELECT `Userid` FROM `tblusers` 
										WHERE `Role` = 'admin'";
						$userarr = $connection->execute($sel_user_qry)->fetchAll('assoc');
						$userid = $userarr[0]['Userid'];
					}
					foreach($explode as $url)
					{
						$url = addslashes($url);
						$url = trim($url);
						if(filter_var($url, FILTER_VALIDATE_URL))
						{
							$select_qry = "SELECT Url FROM tblcrawler_queue WHERE Url='".$url."'";
							$select_arr = $connection->execute($select_qry)->fetchAll('assoc');
							if(count($select_arr)==0)
							{
								$insert_qry = "INSERT INTO tblcrawler_queue (`Url`,`Status`,`Stage`,`Userid`,`Batchid`,Datecreated) 
												VALUES ('".$url."','submitted','auto_extract',".$userid.",".$batchid.",'".$date_created."')";
								$connection->execute($insert_qry);
							}
						}
							
					}
					
					/*$sel_arr = implode("','", $valid);
					$select_qry = "SELECT Url FROM tblcrawler_queue WHERE Url IN('".$sel_arr."') GROUP BY Url";
					$select_arr = $connection->execute($select_qry)->fetchAll('assoc');
					$stagingarr = array_values(array_diff( $valid, array_column($select_arr , 'Url')));
					$stagingcount = count($stagingarr);
					$totalcount = $urlcount - $stagingcount;
					if($stagingcount>0)
					{
						$batch_qry = "SELECT Batchid FROM tblcrawler_queue ORDER BY Batchid DESC LIMIT 1";
						$batch = $connection->execute($batch_qry)->fetchAll('assoc');
						$batchid = $batch[0]['Batchid']+1;
						$date_created = date('Y-m-d H:i:s');
						for($i=0; $i<count($stagingarr); $i++)
						{
							$article[]='("'.$stagingarr[$i].'","submitted","auto_extract","1","'.$batchid.'","'.$date_created.'")';
						}
						
						$insert_qry = "INSERT INTO tblcrawler_queue (`Url`,`Status`,`Stage`,`Userid`,`Batchid`,Datecreated) 
										VALUES ".implode(",", $article);
						
						$connection->execute($insert_qry);

					}*/
					$select_arr = parent::staging($search,$_POST['status'],$_POST['limit']);
					$_SESSION['stage_type']=$search;
					$_SESSION['stage_status']=$_POST['status'];
					$_SESSION['stage_limit'] = $_POST['limit'];
					$this->set('status',$_POST['status']);
					$this->set('limit',$_POST['limit']);
				}
				if($_POST['action']=='edit_view')
				{
					$urlid = $_POST['url_id'];
					$sel_art_arr = parent::edit_view($urlid);
					$this->set('articlearr',$sel_art_arr);
					/*if($_POST['actionname']=='staging')
							$this->set('page',$_POST['actionname']);
						else
							$this->set('page','');*/
					$this->set('render','edit');
					//count of approval
					$approval = parent::needstoapproval();
					$this->set('render','edit_view');
					$this->set('staging',$approval);					
					$this->render('admin_dashboard_staging_filter');
				}
				
				else if($_POST['action']=='edit_changes')
				{
					$art_id = $_POST['article_id'];
					$urlid = $_POST['url_id'];
					$action = $_POST['process'];
					$data = $_POST['data'];
					$editprocess = parent::common_edit($urlid,$art_id,$action,$data);
					if($action=='add_tag')
					{
						echo $editprocess;
						exit;
					}
					if($action=='save' || $action=='re_crawl' || $action=='delete' || $action=='prepublished' || $action=='published')
					{
						
						if($_POST['search']!='')
							$search = $_POST['search'];
						else
							$search = 'all';
						$_SESSION['stage_type']=$search;
						$_SESSION['stage_status']=$_POST['status'];
						$_SESSION['stage_limit'] = $_POST['limit'];


						$select_arr = parent::staging($search,$_POST['status'],$_POST['limit']);
						$this->set('select_arr',$select_arr);
						//count of approval
						$approval = parent::needstoapproval();
						$this->set('staging',$approval);
						$this->set('status',$_POST['status']);
						$this->set('type',$search);
						$this->set('limit',$_POST['limit']);

						return $this->render('admin_dashboard_staging_filter');
						
					}
					else
						exit;
					
				}
				
							
				$this->set('select_arr',$select_arr);
				$this->render('admin_dashboard_staging_filter');
				exit;
			}
			else
			{
				
				$parameters = $this->request->getAttribute('params');
				$page_url = $parameters['?']['page'];
				if($page_url!='')
				{
					$type = $_SESSION['stage_type'];
					$status = $_SESSION['stage_status'];
					$limit = $_SESSION['stage_limit'];
					$this->set('status',$status);
					$this->set('stage_domain',$type);
					$this->set('limit',$limit);
				}
				else
				{
					$type = 'all';
					$status = 'all';
					$limit = 20;
					$_SESSION['stage_type']=$type;
					$_SESSION['stage_status']=$status;
					$_SESSION['stage_limit'] = $limit;
				}
				
				$select_arr = parent::staging($type,$status,$limit);
				$this->set('select_arr',$select_arr);
				//count of approval
				$approval = parent::needstoapproval();
				$this->set('approvalcount',$approval);

				//domain search
				$domain = parent::getdomain('all');
				$this->set('domain',$domain);


			}
		}	
	
	}
	public function adminDashboardLive()
	{
		$connection = ConnectionManager::get('default');
		$this->autoLayout = false;
		$this->set('actionname', $this->request->getParam('action'));
		if($_SESSION['userid']=='')
		{
			$this->redirect(['action' => 'signin']);
			
		}
		else
		{
			
			if($this->request->is('post'))
			{
				if($this->request->is('ajax'))
				{ 
					if($_POST['action']=='Dscore_change')
					{
						$domain = $_POST['domain'];
						$dscore = $_POST['domain_score'];
						$update_qry = "UPDATE `tblranking_score` SET `Domain_score`=".$dscore." 
									WHERE `Domain_name`='".$domain."'";
						$connection->execute($update_qry);
						exit;
					}
					else if($_POST['action']=='edit_view')
					{
						$urlid = $_POST['url_id'];
						$sel_art_arr = parent::edit_view($urlid);
						$this->set('articlearr',$sel_art_arr);
						if($_POST['actionname']=='staging')
								$this->set('page',$_POST['actionname']);
							else
								$this->set('page','');
						$this->set('render','edit');
						//count of approval
						$approval = parent::needstoapproval();
						$this->set('staging',$approval);					
						$this->render('admin_dashboard_live_filter');
					}
					else if($_POST['action']=='edit_changes')
					{
						$art_id = $_POST['article_id'];
						$urlid = $_POST['url_id'];
						$action = $_POST['process'];
						$data = $_POST['data'];
						$editprocess = parent::common_edit($urlid,$art_id,$action,$data);
						if($action=='add_tag')
						{
							echo $editprocess;
							exit;
						}
						if($action=='save' || $action=='re_crawl' || $action=='delete' || $action=='prepublished' || $action=='published')
						{
							if($_POST['actionname']=='staging')
							{
								/*if($_POST['search']!='')
									$search = $_POST['search'];
								else
									$search = 'all';
								$_SESSION['stage_type']=$search;
								$_SESSION['stage_status']=$_POST['status'];
								$_SESSION['stage_limit'] = $_POST['limit'];


								$select_arr = parent::staging($search,$_POST['status'],$_POST['limit']);
								$this->set('select_arr',$select_arr);
								//count of approval
								$approval = parent::needstoapproval();
								$this->set('staging',$approval);
								$this->set('status',$_POST['status']);
								$this->set('type',$search);
								$this->set('limit',$_POST['limit']);

								return $this->render('admin_dashboard_staging_filter');*/
							}
							else
							{
								$domain = $_POST['domain'];
								$limit = $_POST['limit'];
								$filter = $_POST['filter'];

								$_SESSION['domain'] = $domain;
								$_SESSION['limit'] = $limit;
								$_SESSION['live_filter'] = $filter;
								/*  get article and click count for selected article   */		
								$domain_count_arr = parent::select_domain($domain);	
								$this->set('domain_count',$domain_count_arr);

								/*  get article and click count for selected article   */		
								$domain_count_allarr = parent::select_domain('all');	
								$this->set('domain_count_all',$domain_count_allarr);
								
								/*  get all distinct domain with published and unpublished article   */
								$domain_arr = parent::getdomain('published');
								$this->set('domain_arr',$domain_arr);

								/*  get all article with users and clicks   */		
								$all_article = parent::allarticle_domain($domain,$limit,$filter);	
								
								$this->set('all_article',$all_article);
								$this->set('selected_domain',$domain);
								$this->set('limit',$limit);
								$this->set('filter',$filter);
								if($_POST['actionname']=='staging')
									$this->set('page',$_POST['actionname']);
								else
									$this->set('page','');
								//count of approval
								$approval = parent::needstoapproval();
								$this->set('staging',$approval);	
								$this->render('admin_dashboard_live_filter');
							}
						}
						else
							exit;
						
					}
					else
					{
						$domain = $_POST['domain'];
						$limit = $_POST['limit'];
						$filter = $_POST['filter'];
						$_SESSION['domain'] = $domain;
						$_SESSION['limit'] = $limit;
						$_SESSION['live_filter'] = $filter;
						if($_POST['action']=='domain_change' || $_POST['action']=='range_changed' || $_POST['action']=='filter_changed')
						{
							$domain = $_POST['domain'];
							$limit = $_POST['limit'];
							$filter = $_POST['filter'];
						}
						if($_POST['action']=='common_action')
						{
							$url_ids = $_POST['url_ids'];
							$url_ids = array_diff( $url_ids, ['on'] );
							$action = $_POST['flow'];
							$do_common = parent::common($url_ids,$action);
						}
						if($_POST['action']=='change_publish')
						{
							$url_id = $_POST['url_id'];
							$status = $_POST['process'];
							$update_qry = "UPDATE tblcrawler_queue SET `Status` = '".$status."'
										WHERE Url_id =".$url_id;
							$connection->execute($update_qry);
						}
						
						/*  get article and click count for selected article   */		
						$domain_count_arr = parent::select_domain($domain);	
						$this->set('domain_count',$domain_count_arr);

						/*  get article and click count for selected article   */		
						$domain_count_allarr = parent::select_domain('all');	
						$this->set('domain_count_all',$domain_count_allarr);

						/*  get all article with users and clicks   */		
						$all_article = parent::allarticle_domain($domain,$limit,$filter);	
						$this->set('all_article',$all_article);
						$this->set('selected_domain',$domain);
						$this->set('limit',$limit);
						$this->set('filter',$filter);

						/*  get all distinct domain with published and unpublished article   */
						$domain_arr = parent::getdomain('published');
						$this->set('domain_arr',$domain_arr);

						if($_POST['actionname']=='staging')
								$this->set('page',$_POST['actionname']);
							else
								$this->set('page','');
						//count of approval
						$approval = parent::needstoapproval();
						$this->set('staging',$approval);

						//count of published articles
						$publish = parent::needstoapproval('publish');
						$this->set('publishcount',$publish);
								
						$this->render('admin_dashboard_live_filter');
						
					}

				}
				
			}
			else
			{
				/*$domain_arr = parent::getdomain('published');
				$this->set('domain_arr',$domain_arr);*/
				$parameters = $this->request->getAttribute('params');
				$page_url = $parameters['?']['page'];
				if($page_url!='')
				{
					$domain = $_SESSION['domain'];
					$limit = $_SESSION['limit'];
					$filter = $_SESSION['live_filter'];
					
				}
				else
				{
					//$domain = $domain_arr[0]['Domain_name'];
					$domain = 'all';
					$limit = 20;
					if($_SESSION['stage']=='click_approval')
					{
						$filter = 'unpublish';
						$_SESSION['stage']='';
					}
					else
						$filter = 'all';
					$_SESSION['domain'] = $domain;
					$_SESSION['limit'] = $limit;
					$_SESSION['live_filter'] = $filter;

				}
				/*  get article and click count for selected article   */		
				$domain_count_arr = parent::select_domain($domain);	
				$this->set('domain_count',$domain_count_arr);

				/*  get article and click count for selected article   */		
				$domain_count_allarr = parent::select_domain('all');	
				$this->set('domain_count_all',$domain_count_allarr);

				/*  get all article with users and clicks   */		
				$all_article = parent::allarticle_domain($domain,$limit,$filter);	
				$this->set('all_article',$all_article);
				$this->set('selected_domain',$domain);
				$this->set('limit',$limit);
				$this->set('filter',$filter);			

				//count of approval
				$approval = parent::needstoapproval();
				$this->set('approvalcount',$approval);

				/*  get all distinct domain with published and unpublished article   */
				$domain_arr = parent::getdomain('published');
				$this->set('domain_arr',$domain_arr);

				//count of published articles
				$publish = parent::needstoapproval('publish');
				$this->set('publishcount',$publish);
				
			}
			
		}
	}
	public function adminDashboardUsers()
	{
		$this->autoLayout = false;
		$connection = ConnectionManager::get('default');
		if($_SESSION['userid']=='')
		{
			$this->redirect(['action' => 'signin']);
		}
		else
		{
			if($this->request->is('post'))
			{
				if($this->request->is('ajax'))
				{ 
				
					/** users count for current week */
					$sel_count_qry = "SELECT COUNT(`First_name`) AS count FROM 
									tblusers WHERE DATE(`Datecreated`) > DATE_SUB(NOW(), INTERVAL 1 WEEK)";
					$count_arr = $connection->execute($sel_count_qry)->fetchAll('assoc');
					$this->set('countarr',$count_arr);
					if($_POST['action']=='limit_change')
					{
						$limit = $_POST['limit'];
						
						/** get all users */
						$all_users = parent::all_users($limit);
						$user_count = parent::all_users_count();
						$this->set('user_count',$user_count);
						$this->set('allusers',$all_users);
						$this->set('limit',$limit);
						$this->render('admin_dashboard_users_filter');

					}
					if($_POST['action']=='user_process')
					{
						$limit = $_POST['limit'];
						$action = $_POST['process'];
						$userid = $_POST['user_ids'];
						$user_process = parent::user_process($userid,$action);
						
						/** get all users */
						$all_users = parent::all_users($limit);
						$user_count = parent::all_users_count();
						$this->set('user_count',$user_count);
						$this->set('allusers',$all_users);
						$this->set('limit',$limit);
						$this->render('admin_dashboard_users_filter');

					}
					if($_POST['action']=='user_edit')
					{
						$limit = $_POST['limit'];
						$action = $_POST['process'];
						$userid = $_POST['user_id'];
						$user_process = parent::user_edit($userid,$action);
						
						/** get all users */
						$all_users = parent::all_users($limit);
						$user_count = parent::all_users_count();
						$this->set('user_count',$user_count);
						$this->set('allusers',$all_users);
						$this->set('limit',$limit);
						$this->render('admin_dashboard_users_filter');
					}
					if($_POST['action']=='edituser_view')
					{
						$action = $_POST['action'];
						$userid = $_POST['user_id'];
						$user_process = parent::user_editview($userid,$action);
						
						/** get all users */
						$this->set('userarr',$user_process);
						$this->set('render','edit');
						$this->set('limit',$limit);
						$this->render('admin_dashboard_users_filter');
					}
					if($_POST['action']=='edituser_process')
					{
						$limit = $_POST['limit'];
						$action = $_POST['process'];
						$userid = $_POST['user_id'];
						$data = $_POST['formdata'];
						$user_process = parent::edituser_process($userid,$action,$data);

						/** get all users */
						$all_users = parent::all_users($limit);
						$user_count = parent::all_users_count();
						$this->set('limit',$limit);
						$this->set('user_count',$user_count);
						$this->set('allusers',$all_users);
						$this->render('admin_dashboard_users_filter');

					}
					
				}
			}
			else
			{
				/** get all users */
				$limit = 20;
				$all_users = parent::all_users($limit);
				$user_count = parent::all_users_count();
				$this->set('allusers',$all_users);
				$this->set('user_count',$user_count);

				/** users count for current week */
				$sel_count_qry = "SELECT COUNT(`First_name`) AS count FROM 
								tblusers WHERE DATE(`Datecreated`) > DATE_SUB(NOW(), INTERVAL 1 WEEK)";
				$count_arr = $connection->execute($sel_count_qry)->fetchAll('assoc');
				$this->set('countarr',$count_arr);
				

				//count of approval
				$approval = parent::needstoapproval();
				$this->set('approvalcount',$approval);
			}
		}
	}
	public function adminSetting()
	{
		$this->autoLayout = false;
		$connection = ConnectionManager::get('default');
		$sel_batch_qry = "SELECT `Value` FROM `tblconfig` 
								  WHERE `Key_name`='batch_size'";
		$batch_arr = $connection->execute($sel_batch_qry)->fetchAll('assoc');
		$batch_size = $batch_arr[0]['Value'];
		if($_SESSION['userid']=='')
		{
			$this->redirect(['action' => 'signin']);
		}
		else
		{
			if($this->request->is('post'))
			{
				if($this->request->is('ajax'))
				{ 
					if($_POST['action']=='recrawl_setting')
					{
						$date_created = date('Y-m-d H:i:s');
						$update_qry = "UPDATE `tblcrawler_queue` SET `Status`='submitted',`Stage`='auto_extract',
											`Datecreated`='".$date_created."' WHERE `Status` = 'processing'";
										
						$connection->execute($update_qry);
						exit;
					}
				}
				else
				{
					if($_POST['batch_size']!='')
					{
						if($batch_size!='')
						{
							$update_qry = "UPDATE `tblconfig` SET `Value`=".$_POST['batch_size']." 
										WHERE `Key_name`='batch_size'";
							$connection->execute($update_qry);
						}
						else
						{
							$date_created = date('Y-m-d H:i:s');
							$ins_qry = "INSERT INTO `tblconfig`(`Key_name`, `Value`,`Datecreated`) 
										VALUES ('batch_size',".$_POST['batch_size'].",'".$date_created."')";
							$connection->execute($ins_qry);
						}
					}
					if($_POST['confidence']!='')
					{
						$update_qry = "UPDATE `tblconfig` SET `Value`=".$_POST['confidence']." 
							WHERE `Key_name`='confidence'";
						$connection->execute($update_qry);
					}
					if($_POST['confidence']!='')
					{
						$update_qry = "UPDATE `tblconfig` SET `Value`=".$_POST['dscore']." 
							WHERE `Key_name`='domain_score'";
						$connection->execute($update_qry);
					}
				}
				
			}
			$approval = parent::needstoapproval();
			$this->set('approvalcount',$approval);

			/*$cronlog_file = fopen("../bin/cron.log", "r") or die("Unable to open file!");
			//$txtdata =  fread($myfile,filesize("report.txt"));
			$txtdata =  nl2br(file_get_contents("../bin/cron.log"));
			fclose($cronlog_file);*/


			$sel_config_qry = "SELECT `Key_name`,`Value` FROM `tblconfig` 
								WHERE `Key_name`='batch_size' OR 
									  `Key_name` = 'confidence' OR 
									  `Key_name` = 'domain_score'";
			$config_arr = $connection->execute($sel_config_qry)->fetchAll('assoc');
			foreach($config_arr as $config)
			{
				if($config['Key_name']=='batch_size')
					$batch_size = $config['Value'];
				if($config['Key_name']=='confidence')
					$confidence = $config['Value'];
				if($config['Key_name']=='domain_score')
					$domain_score = $config['Value'];
			}
			
			$this->set('batch_size',$batch_size);
			$this->set('confidence',$confidence);
			$this->set('domain_score',$domain_score);
			//$this->set('filedata',$txtdata);
		}
	}
	public function process()
	{
		
		$connection = ConnectionManager::get('default');
		$this->autoLayout = false;
        $start_time = 'Start_time : '.date("Y-m-d H:i:s").';';
		$process_start = microtime(true);
        $queue = parent::queue_analysis();
		$process_end = microtime(true);
		$endtime= ' End_time : '.date("Y-m-d H:i:s").';';
		$tot_process_time = ($process_end - $process_start);
		$tot_process_time = round($tot_process_time,2);
		$duration= ' Duration : '.gmdate('H:i:s', (int)$tot_process_time).PHP_EOL;
        if($queue!='')
		{
			$batch_size = 'Batch count : '.count(explode(',',$queue)).PHP_EOL;
			$sel_comp = "SELECT COUNT(`Url`) as count FROM `tblcrawler_queue` WHERE 
						`Stage`='completed' AND Url_id IN (".$queue.")
						LIMIT 1";
			$comp_arr = $connection->execute($sel_comp)->fetchAll('assoc');
            if($comp_arr[0]['count']!='')
                $comp_count= 'Total completed count(Auto_extract+Monkeylearn) : '.$comp_arr[0]['count'].PHP_EOL;
			else
				$comp_count= '';
			
			/*$sel_comp_auto = "SELECT COUNT(`Url`) as count FROM `tblcrawler_queue` 
							WHERE `Stage`='monkeylearn' AND Url_id IN (".$queue.") LIMIT 1";
			$auto_comp_arr = $connection->execute($sel_comp_auto)->fetchAll('assoc');
            if($auto_comp_arr[0]['count']!='')
                $results.= 'Total auto extract only completed :'.$auto_comp_arr[0]['count'].',';*/

            $sel_ae_error = "SELECT COUNT(`Url`) as count FROM `tblcrawler_queue` 
							WHERE `Status`='error' AND Stage = 'auto_extract' 
							AND Url_id IN (".$queue.") LIMIT 1";
			$ae_error_arr = $connection->execute($sel_ae_error)->fetchAll('assoc');
			$ae_err_count = $ae_error_arr[0]['count'];
            if($ae_err_count>0)
                $ae_error = '	Auto Extract : '.$ae_err_count.PHP_EOL;
			else
				$ae_error = '';
			
			$sel_ml_error = "SELECT COUNT(`Url`) as count FROM `tblcrawler_queue` 
							WHERE `Status`='error' AND Stage = 'monkeylearn' 
							AND Url_id IN (".$queue.") LIMIT 1";
			$ml_error_arr = $connection->execute($sel_ml_error)->fetchAll('assoc');
			$ml_error_count = $ml_error_arr[0]['count'];
            if($ml_error_count>0)
                $ml_error = '	Monkeylearn : '.$ml_error_count.PHP_EOL;
			else
				$ml_error = '';
            
            $results = $start_time.$endtime.$duration.$batch_size.$comp_count;
			if($ae_err_count>0 || $ml_error_count>0)
			{
				$error = 'Error:'.PHP_EOL;
				$error.= $ae_error.$ml_error;
			}
            else
				$error = '';
			
			echo $results.$error;
			
            /*if($results!='')
            {
                $fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
                fwrite($fp, "\r\n".$results);  
                fclose($fp);  
            }*/
		}
		
	}

	public function config()
	{
		$this->autoLayout = false;
		$connection = ConnectionManager::get('default');
		$domain_query = "SELECT DISTINCT(`Domain`) FROM `tblarticle` 
					  ORDER BY `Domain` ASC";
		$domain_arr = $connection->execute($domain_query)->fetchAll('assoc');
		$this->set('domain_arr',$domain_arr);
		if($this->request->is('post'))
		{
			$domain = $_POST['domain'];
			$dscore = $_POST['dcore'];
			$click = $_POST['click'];
			$article_date = $_POST['article_date'];
			$sel_art_qry = 'SELECT `Article_id` FROM `tblarticle` 
							WHERE `Domain`="'.$domain.'"';
			$artid_arr = $connection->execute($sel_art_qry)->fetchAll('assoc');
			if(count($artid_arr)>0)
			{
				for($i=0; $i<count($artid_arr); $i++)
				{
					$article_id = $artid_arr[$i]['Article_id'];
					$sel_rank_qry = "SELECT `Rank_id` FROM `tblranking_score` 
									WHERE `Article_id`=".$article_id;
					$rank_arr = $connection->execute($sel_rank_qry)->fetchAll('assoc');
					if(count($rank_arr)>0)
					{
						$query = 'UPDATE tblranking_score r INNER JOIN tblarticle a ON 
									r.Article_id = a.Article_id 
								 SET r.`Clicks` = '.$click.', 
									r.`Domain_score`="'.$dscore.'", 
									a.Article_date = "'.$article_date.'" 
								WHERE r.Article_id='.$article_id;
						$connection->execute($query);		
					}
					else
					{
						$update_qry = 'UPDATE `tblarticle` SET `Article_date`="'.$article_date.'" 
										WHERE `Article_id`='.$article_id;
						$connection->execute($update_qry);
						$date_created = date('Y-m-d H:i:s');
						$ins_qry = 'INSERT INTO `tblranking_score`(`Article_id`, `Clicks`, `Domain_score`,`Datecreated`) 
								VALUES ('.$article_id.','.$click.',"'.$dscore.'","'.$date_created.'")';
						$connection->execute($ins_qry);
					}
				}
			}
		}
	}
	public function urlprocess()
	{
		$connection = ConnectionManager::get('default');
		if($this->request->is('post'))
		{
			$url = $_POST['url_val'];
			
			$sel_que_qry = 'SELECT * FROM `tblcrawler_queue` WHERE `Url`="'.$url.'"';
			$check_que_arr = $connection->execute($sel_que_qry)->fetchAll('assoc');
			if(count($check_que_arr)==0)
			{
				$sel_que = "SELECT `Batchid` FROM `tblcrawler_queue` 
							ORDER BY `Batchid` DESC LIMIT 1";
				$queue_arr = $connection->execute($sel_que)->fetchAll('assoc');
				if(count($queue_arr)>0)
				{
					$last_batchid = $queue_arr[0]['Batchid'];
					$batchid = $last_batchid+1;
				}
				else
					$batchid = 1;
				$date_created = date('Y-m-d H:i:s');
				$ins_qry = 'INSERT INTO `tblcrawler_queue`
								(`Url`, `Status`, `Stage`, 
								`Userid`, `Batchid`,`Datecreated`) 
							VALUES ("'.$url.'","submitted","auto_extract",
								1,'.$batchid.',"'.$date_created.'")';
				$connection->execute($ins_qry);
			}
			$queue = parent::queue_analysis($url);	
			$sel_crawl_qry = 'SELECT q.`Url`,q.`Status`,q.`Stage`,q.`Status_description`,
							a.`Article_title`,a.Article_id,a.`Article_desc`,a.`Article_date` FROM 
							`tblcrawler_queue` q,tblarticle a WHERE 
							q.`Url`="'.$url.'" AND a.`Url`=q.`Url`';
			$sel_crawl_arr = $connection->execute($sel_crawl_qry)->fetchAll('assoc');
			$this->set('crawl_arr',$sel_crawl_arr);
			$artid_arr = array_column($sel_crawl_arr, 'Article_id');
			$artid = $artid_arr[0];
			$sel_classifi_qry = "SELECT * FROM `tblclassification` WHERE `Article_id`=".$artid;
			$sel_classifi_arr = $connection->execute($sel_classifi_qry)->fetchAll('assoc');
			$this->set('classifi_arr',$sel_classifi_arr);
		}		
	}
	public function paginationTest()
	{
		$connection = ConnectionManager::get('default');
		$this->loadModel('Tblarticle');
		$this->loadModel('Tblusers');
		$this->loadModel('TblcrawlerQueue');
		$this->loadModel('TblrankingScore');
		$query = "SELECT a.`Article_date`,a.Url,a.Article_title,u.Email,a.Clicks,q.Status,q.Url_id 
					  FROM tblarticle a, tblranking_score r,tblusers u,tblcrawler_queue q 
					  WHERE r.`Domain_id` = a.`Domain_id` AND 
					  		q.`Url_id`=a.`Url_id` AND 
							q.Userid = u.Userid AND 
							q.Status LIKE '%published' AND 
							r.Domain_name='earlyretirementnow.com' 
					  ORDER BY a.Article_date DESC";
		
		//$sel_crawl_arr = $connection->execute($query)->fetchAll('assoc');
		//$articles_test = $this->getTableLocator()->get('Tblarticle');
			$articles = $this->Tblarticle->find('all', array(
			'contain' => array('TblrankingScore','TblcrawlerQueue'),
			'conditions' => array(
				//'TblrankingScore.Domain_name = "earlyretirementnow.com"',
				'TblrankingScore.Domain_id = Tblarticle.Domain_id',
				'TblcrawlerQueue.Status LIKE "%published"'
			),
			'order' => 'Tblarticle.Article_title ASC'
		));
		

		$settings = ['limit' => 20,
					    'maxLimit' => 100
					];
		//$query = $this->Tblarticle->find()->where(['Clicks' => 0]);
		$this->set('articles', $this->paginate($articles,$settings));
	}
	
	public function userDashboard()
	{
		$this->autoLayout = false;
		$connection = ConnectionManager::get('default');
		$userid = $_SESSION['userid'];
		if($userid=='')
		{
			$this->redirect(['action' => 'signin']);
		}
		else
		{
			if($this->request->is('ajax'))
			{
				if($_POST['processtype']=='urlsubmit')
				{	
					$url = $_POST['url'];
					$url = trim($url);
					$url = addslashes($url);
					if(filter_var($url, FILTER_VALIDATE_URL))
					{
						$select_qry = "SELECT Url FROM tblcrawler_queue WHERE Url='".$url."' GROUP BY Url";								
						$select_arr = $connection->execute($select_qry)->fetchAll('assoc');
						if(count($select_arr)==0)
						{
							$sel_que = "SELECT `Batchid` FROM `tblcrawler_queue` 
										ORDER BY `Batchid` DESC LIMIT 1";
							$queue_arr = $connection->execute($sel_que)->fetchAll('assoc');
							if(count($queue_arr)>0)
								$batchid = $queue_arr[0]['Batchid']+1;
							else
								$batchid = 1;
							
							$date_created = date('Y-m-d H:i:s');
							
							$ins_qry = "INSERT INTO `tblcrawler_queue`(`Url`, `Status`, `Stage`, `Userid`, `Batchid`,`Datecreated`) 
									VALUES ('".$url."','submitted','auto_extract',".$userid.",".$batchid.",'".$date_created."')";
							$connection->execute($ins_qry);	
						}
						else
							echo 'Url Already Exists';	   
					}
					else
						echo 'Enter Valid Url';	

				}
				exit;
			}
			else
			{	
				if($_SESSION['artid']!='')
				{
					parent::save_article();
				}					
				$saved_arr = parent::savedarticle($userid,'week','',20);
				$this->set('saved_arr',$saved_arr);
				$_SESSION['artid']='';
			}
		}
	}

	public function userDashboardSaved()
	{
		$this->autoLayout = false;
		$connection = ConnectionManager::get('default');
		if($_SESSION['userid']=='')
		{
			$this->redirect(['action' => 'signin']);
		}
		else
		{
			$userid = $_SESSION['userid'];
			if($this->request->is('ajax'))
			{
				if($_POST['action']=='common')
				{
					if($_POST['processtype']=='delete')
					{
						$limit = $_POST['limit'];
						$search = $_POST['search'];
						
						$savedarr = array();
						if($_POST['id']!='')
						{						
							$where="Article_id =".$_POST['id'];
							if($_POST['type']=='userarticle')
							{
								$this->set('render','savedarticle');
								if($search!='')
									$type = 'search';
								else
									$type = 'all';	
							}
							else
							{
								$this->set('render','');
								$type = 'week';
							}
													
						}
						else 
						{
							$savedarr = array_diff( $_POST['savedarr'], ['on'] );
							$where="Article_id IN(". implode(',', $savedarr).")";
							$this->set('render','savedarticle');
							if($search!='')
								$type = 'search';
							else
								$type = 'all';	
						}

						$delete_qry = "DELETE FROM tblsaved_article WHERE ".$where;				
						$connection->execute($delete_qry);

						$saved_arr = parent::savedarticle($userid,$type,$search,$limit);
						$this->set('type',$type);
						$this->set('search',$search);	
						$this->set('limit',$limit);
						$this->set('saved_arr',$saved_arr);
						$this->render('user_filter');
					}
					if($_POST['processtype']=='search')
					{					
						if($_POST['search']!='')
						{
							$type = 'search';
							$search = $_POST['search'];
						}
						else
						{
							$type = 'all'; 
							$search = '';
						}	
						$saved_arr = parent::savedarticle($userid,$type,$search,$_POST['limit']);
						$_SESSION['user_type']=$type;
						$_SESSION['user_search']=$search;
						$_SESSION['user_limit'] = $_POST['limit'];

						$this->set('type',$type);
						$this->set('search',$search);	
						$this->set('limit',$_POST['limit']);

						$this->set('render','savedarticle');
						$this->set('saved_arr',$saved_arr);					
						$this->render('user_filter');	
					}
					
					if($_POST['processtype']=='limit')
					{
						$limit = $_POST['limit'];
						if($_POST['search']!='')
						{
							$search = $_POST['search'];
							$type = 'search';
						}
						else
						{
							$search = '';
							$type = 'all';
						}	
						$saved_arr = parent::savedarticle($userid,$type,$search,$limit);
						$_SESSION['user_type']=$type;
						$_SESSION['user_search']=$search;
						$_SESSION['user_limit'] = $limit;
						
						$this->set('search',$search);	
						$this->set('limit',$limit);

						$this->set('render','savedarticle');
						$this->set('saved_arr',$saved_arr);					
						$this->render('user_filter');
					}
				}
				exit;	
			}
			else
			{
				$parameters = $this->request->getAttribute('params');
				$page_url = $parameters['?']['page'];
				if($page_url!='')
				{
					$type=$_SESSION['user_type'];
					$search=$_SESSION['user_search'];
					$limit=$_SESSION['user_limit'];
					$_SESSION['user_page'] = $page_url;
					$this->set('search',$search);
					$this->set('limit',$limit);
				}
				else
				{
					$type = 'all';
					$search = '';
					$limit = 20;
					$_SESSION['user_type']=$type;
					$_SESSION['user_search']=$search;
					$_SESSION['user_limit'] = $limit;
					$_SESSION['user_page'] = 1;

				}

				$saved_arr = parent::savedarticle($userid,$type,$search,$limit);
				$this->set('saved_arr',$saved_arr);

			}	
		}			
	}
	public function about()
	{				$connection = ConnectionManager::get('default');				if($this->request->is('post'))		{			if($this->request->is('ajax'))			{				if($_POST['action']=='article_search')				{										$_SESSION['redirect_header'] = 'true';										$_SESSION['filter'] = 'all';										if($_POST['tag_select']!='')											{						$_SESSION['index_search'] = $_POST['tag_select'];												$_SESSION['tag'] = $_POST['tag_select'];												$_SESSION['match_tag'] = $_POST['tag_select'];											}										if($_POST['index_search']!='')											{												$_SESSION['index_search'] = $_POST['index_search'];													$str = $_POST['index_search'];													$check_tag_qry = "SELECT DISTINCT(Tags) FROM `tblclassification` 										  WHERE LOWER(`Tags`) = '".strtolower($str)."'";												$check_tag_arr = $connection->execute($check_tag_qry)->fetchAll('assoc');																			$check_tag = $check_tag_arr[0]['Tags'];												if($check_tag!='')													{							$_SESSION['tag'] = $check_tag;														$_SESSION['match_tag'] = $check_tag;														$responsearr = parent::searcharticle();						}												else													{						 							$str_arr = explode(" ",$str);							$arr = '';							foreach($str_arr as $val)							{								if(strlen($val)>3)								{									$val = strtolower($val);									$val = preg_replace('/[^A-Za-z0-9\-]/', '', $val);									$wh_arr = array('','will','what','where','shall','when','could','should','would','your','whats');									$space = ' ';									if(array_search($val,$wh_arr)=='')										$arr.=$space.trim($val).$space.'|';								}															}							$arr_val = rtrim($arr,'|');							$_SESSION['filter'] = $arr_val;							$_SESSION['seo'] = '';														$_SESSION['tag'] = '';														$_SESSION['match_tag'] = '';											}					}									}								exit;							}					}				$datalist_tags = parent::datalist_tag();					$this->set('datalist_tags',$datalist_tags);
	}
	public function contact()
	{
		$connection = ConnectionManager::get('default');		
		if($this->request->is('post'))
		{			if($this->request->is('ajax'))			{				if($_POST['action']=='article_search')				{										$_SESSION['redirect_header'] = 'true';										$_SESSION['filter'] = 'all';																		if($_POST['tag_select']!='')											{						$_SESSION['index_search'] = $_POST['tag_select'];												$_SESSION['tag'] = $_POST['tag_select'];												$_SESSION['match_tag'] = $_POST['tag_select'];											}										if($_POST['index_search']!='')											{												$_SESSION['index_search'] = $_POST['index_search'];													$str = $_POST['index_search'];													$check_tag_qry = "SELECT DISTINCT(Tags) FROM `tblclassification` 										  WHERE LOWER(`Tags`) = '".strtolower($str)."'";												$check_tag_arr = $connection->execute($check_tag_qry)->fetchAll('assoc');																			$check_tag = $check_tag_arr[0]['Tags'];												if($check_tag!='')													{							$_SESSION['tag'] = $check_tag;														$_SESSION['match_tag'] =$check_tag;														$responsearr = parent::searcharticle();						}												else													{						 							$str_arr = explode(" ",$str);							$arr = '';							foreach($str_arr as $val)							{								if(strlen($val)>3)								{									$val = strtolower($val);									$val = preg_replace('/[^A-Za-z0-9\-]/', '', $val);									$wh_arr = array('','will','what','where','shall','when','could','should','would','your','whats');									$space = ' ';									if(array_search($val,$wh_arr)=='')										$arr.=$space.trim($val).$space.'|';								}															}							$arr_val = rtrim($arr,'|');							$_SESSION['filter'] = $arr_val;														$_SESSION['tag'] = ''; 														$_SESSION['match_tag'] = '';											}					}									}								exit;			}						else						{			
				$fromaddress = $_POST['email'];
				$toaddress = 'hjayne2@yahoo.com';								$subject = 'Retirementcasa contact form';
				
				$message = 'FirstName  : '.$_POST['firstName'].'<br>  
							LastName : '.$_POST['lastName'].'<br>
							Email : '.$_POST['email'].'<br>
							Message : '.addslashes($_POST['contact_text']);
				$FromName = $_POST['firstName']. ' '.$_POST['lastName'];			
				$this->sendmail($fromaddress,$FromName,$toaddress,$subject,$message);						}
		}				$datalist_tags = parent::datalist_tag();					$this->set('datalist_tags',$datalist_tags);
	}
	public function dbchange()
	{
		$connection = ConnectionManager::get('default');				
		
	}
	public function sendmail($fromaddress,$fromname,$toaddress,$subject,$message)
	{				
		$mailer = new PHPMailer();
		$mailer->IsSMTP();
		$mailer->Host = "mail.myretirementrover.com";
		$mailer->SMTPAuth = true; 
		$mailer->Username = "contact@myretirementrover.com"; 
		$mailer->Password = "Housty70_bh!";
		$mailer->Port = 587; //465	
		$mailer->IsHTML(true);
		$mailer->From = $fromaddress;
		$mailer->FromName = $fromname;
		$mailer->AddAddress($toaddress);
		$mailer->Subject = $subject;     
		$mailer->Body = $message;  
		$mailer->AltBody = "This is a multi-part message in MIME format. If you are reading this, please update your email client to one that can support multi-part messages.to";
		$mailer->Send();
		
		
	}
	
}