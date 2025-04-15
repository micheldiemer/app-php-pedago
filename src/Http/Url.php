<?php

namespace App\Http;

/**
 * @see https://www.php.net/manual/fr/function.parse-url.php
 */

class Url
{
    public function __construct(?string $hostname, ?string $path)
    {
        if (empty($hostname) && empty($path)) {
            throw new \InvalidArgumentException('Url : hostname ou path doit être défini');
        }
        $this->hostname = $hostname;
        $this->path = $path;
    }

    public static function fromString(?string $strurl): Url
    {
        if (empty($strurl)) {
            throw new \InvalidArgumentException('Url : ne peut être vide');
        }
        $parsed = parse_url($strurl);
        $url = new Url($parsed['host'] ?? null, $parsed['path'] ?? null);

        $url->scheme = $parsed['scheme'] ?? null;
        if (isset($parsed['port'])) {
            $url->port = intval($parsed['port']);
        }
        $url->user = $parsed['user'] ?? null;
        $url->password = $parsed['pass'] ?? null;
        $url->fragment = $parsed['fragment'] ?? null;

        $query_string = $parsed['query'] ?? null;
        if (is_null($query_string))
            return $url;

        parse_str($parsed['query'], $params);
        foreach ($params as $k => $v) {
            $url->setParam($k, $v);
        }
        return $url;
    }

    /**
     * query parameters (for query string)
     */
    private array $params = [];

    /**
     * http / https
     */
    public ?string $scheme = null {
        set(?string $value) {
            if (is_null($value)) {
                $this->scheme = null;
                return;
            }

            $l = trim(strtolower($value));
            if (!in_array($l, ['http', 'https'])) {
                throw new \InvalidArgumentException('Scheme ' . $value . ' invalide');
            }
            $this->scheme = 'http';
        }
    }

    /**
     * ip or name exemple localhost, 127.0.0.1, app.lan, site.fr
     */
    public ?string $hostname = null {
        set(?string $value) {
            $this->hostname = $value;
        }
    }

    /**
     * port 0-65535 exemple : 80,443,8000,4430
     */
    public ?int $port = null {
        set(?int $value) {
            if (is_null($value)) {
                $this->port = null;
                return;
            }
            if ($value <= 0 || $value > 2 ** 16 - 1) {
                throw new \InvalidArgumentException("Port invalide $port");
            }
            $this->port = $value;
        }
    }

    /**
     * path exemple : /article/create
     */
    public ?string $path = null {
        set(?string $value) {
            if (is_null($value)) {
                $this->path = null;
                return;
            }
            $this->path = $value;

            // $pos = stripos('/',$value,$i);
            // foreach ($paths as $path) {
            //     $this->path .= rawurlencode($path);
            // }
        }
    }

    /**
     * lien interne au document : #titre1
     */
    public ?string $fragment = null {
        set(?string $value) {
            if (is_null($value)) {
                $this->fragment = $value;
                return;
            }
            $this->fragment = rawurlencode($value);
        }
    }



    /**
     * user
     */
    public ?string $user = null {
        set(?string $value) {
            if (empty($value)) {
                $this->user = null;
                return;
            }
            $this->user = rawurlencode($value);
        }
    }


    /**
     * password
     */
    public ?string $password = null {

        set(?string $value) {
            if (empty($value)) {
                $this->password = null;
                return;
            }
            $this->password = rawurlencode($value);
        }
    }

    /**
     * validateParamValue
     *
     * @param string $param         : nom du paramètre à valider
     * @param \callable $callback   : nom de la fonction de rappel
     *   doit renvoyer strictement true pour que le paramètre soit valide
     * @return boolean              : true=>le paramètre est valide
     *                                false=>le paramètre n'est pas valide
     *
     * Exemple :
     *
     * $url = ...
     * function ageValue($age) {
     *   $i = intval($age);
     *   if($i == 0 && $age !== 0) return false;
     *   return $i >= 0 && $i <= 150;
     *  }
     *
     *  $url->setParam('age',52);
     *  if(validateParamValue('age','ageValue')) {
     *     echo 'age correct';
     *  }
     *
     *  $url->setParam('age','efefez');
     *  if(!validateParamValue('age','ageValue')) {
     *     echo 'age incorrect';
     *  }
     *
     */
    public function validateParamValue(string $param, string $callback): bool
    {
        if (call_user_func($callback, $this->params[$param] ?? null, $param) === true) {
            return true;
        }
        return false;
    }

    public function setParam(string $param, string|array|null $value)
    {
        if (is_null($value)) {
            $this->removeParam($param);
            return;
        }
        $this->params[$param] = $value;
    }

    public function removeParam(string $param)
    {
        unset($this->params[$param]);
    }


    public function __toString()
    {
        $url = '';
        if (!is_null($this->scheme)) {
            $url = $this->scheme . '://';
        }

        if (!is_null($this->hostname)) {
            // URL absolue

            if (!is_null($this->user) && !is_null($this->password)) {
                $url .= $this->user . ':' . $this->password . '@';
            }
            $url .= $this->hostname;

            if (!is_null($this->port)) {
                $url .= ':' . $this->port;
            }

            if (
                !is_null($this->path)
                && !str_starts_with($this->path, '/')
            ) {
                $url .= '/';
            }
        }

        $url .= ($this->path ?? '');

        if (!is_null($this->fragment)) {
            $url .= "#" . $this->fragment;
        }

        if (!empty($this->params)) {
            $url .= '?' . http_build_query($this->params);
        }

        return $url;
    }
}
