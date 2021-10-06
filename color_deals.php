<?php


class ColorDeals {
    public $all_leads = [];
    public $all_contacts = [];
    public $all_companies = [];
    public $color_deals_conditions = false;


    public function __construct($apiClient) {
        $this->apiClient = $apiClient;
    }

    public function setAll($entity) {
        $args = array(
            'limit_rows' => 250,
            'limit_offset' => 0
        );
        $limit = 250;
        $iter = 1;
        $count = 0;
        $contacts_array = [];


        if ($entity == 'leads') {
            try {
                $leadsService = $this->apiClient->leads();
                while(true){
                    $leads = $leadsService->get(null, [], $limit, $iter);                

                    $leads = $leads->toArray();
                    foreach($leads as $lead){
                        $this->all_leads[] = $lead;
                    }

                    if (count($leads) < $limit) {
                        break;
                    } else {
                        $iter++;
                    }
                }
            } catch (\Throwable $th) {
                
            }
        } else if ($entity == 'contacts') {
            try {
                $contacts_array = [];
                $contactsService = $this->apiClient->contacts();
                while(true){
                    $contacts = $contactsService->get(null, [], $limit, $iter);                

                    $contacts = $contacts->toArray();
                    foreach($contacts as $contact){
                        $contacts_array[] = $contact;
                    }

                    if (count($contacts) < $limit) {
                        break;
                    } else {
                        $iter++;
                    }
                }
                $this->all_contacts = array_map(function ($el) {
                    return array(
                        'name' => $el['name'],
                        'id' => $el['id']
                    );
                }, $contacts_array);
            } catch (\Throwable $th) {

            }
        } else if ($entity == 'companies') {
            try {
                $companiesService = $this->apiClient->companies();
                while(true){
                    $companies = $companiesService->get(null, [], $limit, $iter);                

                    $companies = $companies->toArray();
                    foreach($companies as $company){
                        $this->all_companies[] = $company;
                    }

                    if (count($companies) < $limit) {
                        break;
                    } else {
                        $iter++;
                    }
                }
            } catch (\Throwable $th) {
                
            }
            
        }      
    }
    public function writeAllToFile($entity, $data){
        $path = BASE_PATH . $entity . '.json';
        $saving_data = json_encode($data, JSON_UNESCAPED_UNICODE);
        file_put_contents($path, $saving_data);
    }
    public function getAllFromFile($entity) {
        $path = BASE_PATH . $entity . '.json';
        if (file_exists($path)) {
            $file_content = file_get_contents($path);

            if ($file_content) {
                return json_decode($file_content, true);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function getDealsByConditions($conditions, $leads, $contacts, $companies, $users) {
        $output_color = $conditions['color'];
        $isBg = ($conditions['isBg'] === 'true' || $conditions['isBg'] === true) ? : '';
        $isBorder = ($conditions['isBorder'] === 'true' || $conditions['isBorder'] === true) ? : '';
        $deals_arrays = [];
               
        foreach ($conditions['settings'] as $key => $settings) {
            $filter = new DealsFilter($settings, $leads, $contacts, $companies, $users);
            $title = $settings['title'];

            switch ($title) {
                case 'Бюджет':
                    $deals_arrays[] = $filter->filterByPrice();
                    break;
                case 'Название компании':
                    $deals_arrays[] = $filter->filterByCompanyName();
                    break;
                case 'Имя контакта':
                    $deals_arrays[] = $filter->filterByContactName();
                    break;
                case 'Тег':
                    $deals_arrays[] = $filter->filterByTag();
                    break;
                case 'Дата создания':
                    $deals_arrays[] = $filter->filterByDateCreation();
                    break;
                case 'Ответственный':
                    $deals_arrays[] = $filter->filterByUser();
                    break;
                default: 
                    $deals_arrays[] = $filter->filterByCF();
                    break;
            }

        }
        $output_array = $deals_arrays[0];
        foreach ($deals_arrays as $key => $value) {
            $output_array = array_intersect($output_array, $value);
        }

        $output_deals = [];
        foreach ($output_array as $index => $value) {
            $output_deals[] = $value;
        }
        
        return array(
            'color' => $output_color,
            'isBg' => $isBg,
            'isBorder' => $isBorder,
            'deals' => $output_deals
        );
    }
    public function saveColorDealsSettings($need_renew = false) {
        $all_leads = $this->getAllFromFile('leads');
        if (!$all_leads || $need_renew) {
            $this->setAll('leads');
            $this->writeAllToFile('leads', $this->all_leads);
        } else {
            $this->all_leads = $all_leads;
        }
        $all_contacts = $this->getAllFromFile('contacts');
        if (!$all_contacts || $need_renew) {
            $this->setAll('contacts');
            $this->writeAllToFile('contacts', $this->all_contacts);
        } else {
            $this->all_contacts = $all_contacts;
        }
        $all_companies = $this->getAllFromFile('companies');
        if (!$all_companies || $need_renew) {
            $this->setAll('companies');
            $this->writeAllToFile('companies', $this->all_companies);
        } else {
            $this->all_companies = $all_companies;
        }

        $usersService = $this->apiClient->users();
        $usersCollection = $usersService->get();
        $users = $usersCollection->toArray();
        $this->all_users = array_map(function($el) {
            return array(
                'id' => $el['id'],
                'name' => $el['name']
            );
        }, $users);
        
        
        $conditions = $this->color_deals_conditions ? : $_REQUEST['data'];

        // echo '<pre>';
        // print_r($this->color_deals_conditions);
        // echo '</pre>';
        // return;

        $data = [];
        foreach ($conditions as $key => $condition) {
            $data[] = $this->getDealsByConditions($condition, $this->all_leads, $this->all_contacts, $this->all_companies, $this->all_users);
        }

        // сохранение настроек виджета
        $saving_data = array(
            'conditions' => $conditions,
            'deals' => $data
        );

        file_put_contents(SETTINGS_FILE, json_encode($saving_data, JSON_UNESCAPED_UNICODE));

        // $saving_data = json_encode($saving_data, JSON_UNESCAPED_UNICODE);
    }
}



class DealsFilter {
    private $conditions;
    private $amo;
    private $leads = [];
    private $companies = [];
    private $contacts = [];

    public function __construct($conditions, $leads, $contacts, $companies, $users) {
        $this->conditions = $conditions;
        $this->leads = $leads;
        $this->contacts = $contacts;
        $this->companies = $companies;
        $this->users = $users;
    }

    public function filterByPrice() {
        $output_array = [];
        $min_price = $this->conditions['value1'] ? : 0;
        $max_price = $this->conditions['value2'] ? : INF;

        if ($max_price <= $min_price) {
            $max_price = $min_price;
        }

        foreach ($this->leads as $index => $fields) {
            if ($fields['price'] >= $min_price && $fields['price'] <= $max_price) {
                $output_array[] = $fields['id'];
            }
        }

        return $output_array;
    }
    public function filterByContactName() {
        $output_array = [];
        $current_contact = $this->conditions['value1'];

        $current_contact_id = '';
        foreach ($this->contacts as $key => $value) {
            if (cleaningJson($current_contact, true) == cleaningJson($value['name'], true)) {
                $current_contact_id = $value['id'];
                break;
            }
        }

        // echo $current_contact_id . PHP_EOL;

        foreach ($this->leads as $index => $fields) {
            if ($fields['main_contact_id'] == $current_contact_id) {
                $output_array[] = $fields['id'];
                // echo $fields['main_contact_id'] . PHP_EOL;
            }
        }
        return $output_array;
    }
    public function filterByUser() {
        $output_array = [];
        $current_username = $this->conditions['value1'];

        $current_user_id = '';
        foreach ($this->users as $key => $value) {
            if (cleaningJson($current_username, true) == cleaningJson($value['name'], true)) {
                $current_user_id = $value['id'];
                break;
            }
        }

        foreach ($this->leads as $index => $fields) {
            if ($fields['responsible_user_id'] == $current_user_id) {
                $output_array[] = $fields['id'];
                // echo $fields['main_contact_id'] . PHP_EOL;
            }
        }
        return $output_array;
    }
    public function filterByCompanyName() {
        $output_array = [];
        $current_company = $this->conditions['value1'];

        foreach ($this->companies as $i => $company) {
            if (cleaningJson($company['name'], true) == cleaningJson($current_company, true)) {
                $output_array = $company['linked_leads_id'];
                break;

            }
        }
        
        return $output_array;
    }
    public function filterByTag() {
        $output_array = [];
        $current_tag = $this->conditions['value1'];

        foreach ($this->leads as $index => $fields) {
            $tags = $fields['tags'];

            if (empty($tags)) continue;

            foreach ($tags as $index => $tag) {
                if (cleaningJson($tag['name'], true) == cleaningJson($current_tag, true)) {
                    $output_array[] = $fields['id'];
                    break;
                }
            }
            
        }

        return $output_array;
    }
    public function filterByCF() {
        $output_array = [];

        $current_field = $this->conditions['title'];
        $current_value = $this->conditions['value1'] ? : INF;
        $inputs_match = $this->conditions['inputs'];
        $max_value = $this->conditions['value2'] ? : INF;
        

        foreach ($this->leads as $index => $fields) {
            if (isset($fields['custom_fields_values'])) {
                
                foreach ($fields['custom_fields_values'] as $ind => $field) {
                    if ($field['field_name'] == $current_field) {

                        foreach ($field['values'] as $i => $value) {
                            if ($inputs_match == 'double') {
                                $pattern = '/^[0-9]{4}[-]{1}[0-9]{2}[-]{1}[0-9]{2} [0-9]{2}[:]{1}[0-9]{2}[:]{1}[0-9]{2}$/';
                                if (preg_match($pattern, $value['value'])) {
                                    $val = strtotime($value['value']) * 1000;

                                    if ($val > $current_value && $val < $max_value) {
                                        $output_array[] = $fields['id'];
                                        break;
                                    }
                                } else {
                                    if ($value['value'] > $current_value && $value['value'] < $max_value) {
                                        $output_array[] = $fields['id'];
                                        break;
                                    }
                                }
                            } else {
                                if (cleaningJson($value['value'], true) == cleaningJson($current_value, true)) {
                                    $output_array[] = $fields['id'];
                                    break;
                                }
                            }
                            
                        }
                        
                    } 
                }

            } 
        }

        return $output_array;
    }
    public function filterByDateCreation() {
        $output_array = [];
        $min_price = $this->conditions['value1'] ? : 0;
        $max_price = $this->conditions['value2'] ? : INF;

        if ($max_price <= $min_price) {
            $max_price = $min_price;
        }

        foreach ($this->leads as $index => $fields) {
            if ($fields['created_at'] * 1000 >= $min_price && $fields['created_at'] * 1000 <= $max_price) {
                $output_array[] = $fields['id'];
            }
        }

        return $output_array;
    }
}

if (!function_exists('writeToLog')) {
	function writeToLog($data, $title = '') { 
		$log = "\n------------------------\n"; 
		$log .= date("Y.m.d G:i:s") . "\n"; 
		$log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n"; 
		$log .= print_r($data, 1); 
        $log .= "\n------------------------\n"; 
		file_put_contents(__DIR__ . '/hook.log', $log, FILE_APPEND); 
		return true; 
	} 
}

function cleaningJson($string, $hard = false) {
    $string = str_replace('\\\"', '', $string);
    $string = str_replace('\\\'', '', $string);

    if ($hard) {
        $string = str_replace('\\', '', $string);
        $string = str_replace('\'', '', $string);
        $string = str_replace('\"', '', $string);
        $string = str_replace('"', '', $string);
        $string = str_replace('\`', '', $string);
    }

    return $string;
}
function prependDataToJson($string) {
    $string = str_replace('"', '\"', $string);
    return $string;
}
function escapeArrayStrings($array) {
    $return = [];
    if (is_array($array)) {
        foreach ($array as $index => $value) {
            $return[$index] = escapeArrayStrings($value);
        }
        return $return;
    } else {
        return str_replace('"', '\"', $array);
    }
}

