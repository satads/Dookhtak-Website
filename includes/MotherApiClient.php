<?php
/**
 * Mother-system adapter. Reads the MOTHER_API config block
 * (includes/config.php) — placeholders until the mother API docs arrive.
 * With enabled=false nothing is sent and submissions stay 'pending';
 * the site works fully either way.
 */

require_once __DIR__ . '/config.php';

class MotherApiClient
{
    /** @var array{enabled:bool,base_url:string,endpoint:string,api_key:string,instance_id:string} */
    private array $config;

    public function __construct(?array $config = null)
    {
        // TODO: fill from mother API docs (base_url, endpoint, api_key, instance_id)
        $this->config = $config ?? (defined('MOTHER_API') ? MOTHER_API : ['enabled' => false]);
    }

    public function enabled(): bool
    {
        return !empty($this->config['enabled']);
    }

    /**
     * Forward one submission to the mother system.
     * $data keys: name, phone, business_type, subject, message, submitted_at.
     * Returns ['ok' => bool, 'error' => string|null, 'status' => int|null].
     */
    public function forwardSubmission(array $data): array
    {
        if (!$this->enabled()) {
            return ['ok' => false, 'error' => 'mother API disabled', 'status' => null];
        }

        $url = rtrim($this->config['base_url'] ?? '', '/') . '/' . ltrim($this->config['endpoint'] ?? '', '/');
        $payload = json_encode([
            'name'          => (string) ($data['name'] ?? ''),
            'phone'         => (string) ($data['phone'] ?? ''),
            'business_type' => (string) ($data['business_type'] ?? ''),
            'subject'       => (string) ($data['subject'] ?? ''),
            'message'       => (string) ($data['message'] ?? ''),
            'submitted_at'  => (string) ($data['submitted_at'] ?? date('c')),
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-Api-Key: ' . ($this->config['api_key'] ?? ''),
                'X-Instance-Id: ' . ($this->config['instance_id'] ?? ''),
            ],
        ]);
        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($errno !== 0) {
            return ['ok' => false, 'error' => 'cURL: ' . $error, 'status' => null];
        }
        if ($status < 200 || $status >= 300) {
            return ['ok' => false, 'error' => 'HTTP ' . $status . ' ' . mb_substr((string) $body, 0, 300), 'status' => $status];
        }
        return ['ok' => true, 'error' => null, 'status' => $status];
    }
}

/**
 * Save-first forward flow: attempt delivery for a stored submission and
 * record the outcome (forward_status / forward_attempts / last_forward_error).
 * Used by api/contact.php on arrival and by the inbox retry button.
 */
function mother_forward_submission(int $submission_id): array
{
    require_once __DIR__ . '/db.php';
    $stmt = db()->prepare('SELECT * FROM submissions WHERE id = ?');
    $stmt->execute([$submission_id]);
    $sub = $stmt->fetch();
    if (!$sub) {
        return ['ok' => false, 'error' => 'submission not found'];
    }

    $client = new MotherApiClient();
    if (!$client->enabled()) {
        return ['ok' => false, 'error' => 'disabled'];
    }

    $result = $client->forwardSubmission([
        'name'          => $sub['name'],
        'phone'         => $sub['phone'],
        'business_type' => $sub['business_type'],
        'subject'       => $sub['subject'],
        'message'       => $sub['message'],
        'submitted_at'  => date('c', strtotime($sub['created_at'])),
    ]);

    db()->prepare(
        'UPDATE submissions SET forward_status = ?, forward_attempts = forward_attempts + 1, last_forward_error = ? WHERE id = ?'
    )->execute([
        $result['ok'] ? 'sent' : 'failed',
        $result['ok'] ? null : mb_substr((string) $result['error'], 0, 500),
        $submission_id,
    ]);

    return $result;
}
