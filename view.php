<?php 

$dir = $_REQUEST['data']['domain'];
define('BASE_DOMAIN', $dir);
define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR);
define('TOKEN_FILE', BASE_PATH . 'token_info.json');
define('AMO_FILE', BASE_PATH . 'amo_info.json');
define('SETTINGS_FILE', BASE_PATH . 'settings.json');



include_once __DIR__ . '/bootstrap.php';
include_once __DIR__ . '/color_deals.php';
use AmoCRM\Helpers\EntityTypesInterface;    


$settings = file_get_contents(SETTINGS_FILE);

    

$AmoAuth = new AmoAuth();
$apiClient = $AmoAuth->getApiClient();
$ColorDeals = new ColorDeals($apiClient);



$contactsService = $apiClient->contacts();
$all_contacts = $contactsService->get(null, [], 50)->toArray();
$contacts_name = array_map(function ($el) {
    return $el['name'];
}, $all_contacts);



$companiesService = $apiClient->companies();
$all_companies = $companiesService->get(null, [], 50)->toArray();
$companies_name = array_map(function ($el) {
    return $el['name'];
}, $all_companies);



$tagsCollection = $apiClient->tags(EntityTypesInterface::LEADS)->get($tagsFilter);
$tags_list_unique = array_map(function ($el) {
    return $el['name'];
}, $tagsCollection->toArray());


$managers_name = [];
$usersService = $apiClient->users();
$usersCollection = $usersService->get();
$users = $usersCollection->toArray();
foreach ($users as $index => $user) {
    $managers_name[] = $user['name'];
}



$lead_statuses = $data['account']['leads_statuses'];
$custom_fields = $data['account']['custom_fields'];
$custom_fields_contacts = $custom_fields['contacts'];
$custom_fields_leads = $custom_fields['leads'];
$custom_fields_companies = $custom_fields['companies'];


$customFieldsService = $apiClient->customFields(EntityTypesInterface::LEADS);
$custom_fields_leads = $customFieldsService->get()->toArray();

$custom_fields_leads = array_map(function ($el) {
    return array(
        'name' => $el['name'],
        'type' => $el['type_id'],
        'variables' => isset($el['enums']) ? $el['enums'] : 'false'
    );
}, $custom_fields_leads);

$conditions = isset($settings['conditions']) ? $settings['conditions'] : false;


echo '
<script>
    window.market = {
        ColorDeal: {
            Conditions: ' . ( $conditions ?  str_replace('"false"', '""', json_encode($conditions, JSON_UNESCAPED_UNICODE)) : '""') . ',
            Variants: [
                {
                    name: "Тег",
                    type: "string",
                    variables: ' . json_encode($tags_list_unique) . '
                }, ';
                foreach ($custom_fields_leads as $index => $field_settings) {
                    $type = 'string';
                    if ($field_settings['type'] == 2) {
                        $type = 'integer';
                    } else if ($field_settings['type'] == 6) {
                        $type = 'date';
                    }

                    $vars = [];
                    if (is_array($field_settings['variables'])) {
                        foreach ($field_settings['variables'] as $key => $value) {
                            $vars[] = $value['value'];
                        };
                    } else {
                        $vars = 'false';
                    }
                    

                    echo '
                        {
                            name: "' . $field_settings['name'] . '",
                            type: "' . $type . '",
                            variables: ' . (is_array($vars) ? json_encode($vars, JSON_UNESCAPED_UNICODE) : 'false') . '
                        },
                    ';
                }
        echo' ]
        },
        Companies: {
            Names: ' . json_encode($companies_name, JSON_UNESCAPED_UNICODE) . '
        },
        Contacts: {
            Names: ' . json_encode($contacts_name, JSON_UNESCAPED_UNICODE) . '
        },
        Managers: {
            Names: ' . json_encode($managers_name, JSON_UNESCAPED_UNICODE) . '
        },
        FieldTypes: AMOCRM.cf_types,
    }
</script>
';



require_once './dist/index.html';

