<?php

namespace APP\plugins\generic\plaudit\classes\migration;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use PKP\install\DowngradeNotSupportedException;
use APP\plugins\generic\plaudit\classes\api\APIKeyEncryption;

class EncryptLegacyCredentials extends Migration
{
    private const PLUGIN_NAME_SETTINGS = 'plauditplugin';
    private const PLUGIN_CREDENTIALS_SETTINGS = [
        'integration_token',
    ];

    public function up(): void
    {
        $credentialSettings = $this->getCredentialSettings();

        if (!empty($credentialSettings)) {
            $encrypter = new APIKeyEncryption();

            foreach ($credentialSettings as $credentialSetting) {
                $credentialSetting = get_object_vars($credentialSetting);

                if ($encrypter->textIsEncrypted($credentialSetting['setting_value'])) {
                    continue;
                }
                $this->encryptCredential($credentialSetting);
            }
        }
    }

    public function down(): void
    {
        throw new DowngradeNotSupportedException();
    }

    private function getCredentialSettings()
    {
        return DB::table('plugin_settings')
            ->where('plugin_name', self::PLUGIN_NAME_SETTINGS)
            ->whereIn('setting_name', self::PLUGIN_CREDENTIALS_SETTINGS)
            ->get();
    }

    private function encryptCredential($credentialSetting)
    {
        $encrypter = new APIKeyEncryption();
        $encryptedSettingValue = $encrypter->encryptString($credentialSetting['setting_value']);

        DB::table('plugin_settings')
            ->where('context_id', $credentialSetting['context_id'])
            ->where('plugin_name', self::PLUGIN_NAME_SETTINGS)
            ->where('setting_name', $credentialSetting['setting_name'])
            ->update(['setting_value' => $encryptedSettingValue]);
    }
}
