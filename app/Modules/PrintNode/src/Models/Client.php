<?php

namespace App\Modules\PrintNode\src\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class Client.
 *
 * @property int                             $id
 * @property string                          $api_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'modules_printnode_clients';

    /**
     * @var string[]
     */
    protected $fillable = ['api_key'];

    /**
     * @var GuzzleClient
     */
    private GuzzleClient $guzzleClient;

    /**
     * Client constructor.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->guzzleClient = new GuzzleClient([
            'base_uri'   => 'https://api.printnode.com',
            'timeout'    => 60,
            'exceptions' => true,
        ]);
    }

    public function fullUrl(string $uri): string
    {
        return 'https://api.printnode.com/' . $uri;
    }

    public function getRequest(string $uri): ResponseInterface
    {
        return $this->guzzleClient->get($uri, ['headers' => $this->generateHeaders()]);
    }

    public function postRequest(string $uri, array $json): Response
    {
        $url = $this->fullUrl($uri);

        $token = base64_encode($this->api_key);

        return Http::withToken($token, 'Basic')
            ->post($url, $json);
    }

    /**
     * @return string[]
     */
    public function generateHeaders(): array
    {
        return  [
            'Authorization' => 'Basic '.base64_encode($this->api_key),
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(Client::class);
    }
}
