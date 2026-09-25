<?php
// Lab 4 - WITH Decorator (GOOD starter, live API, has TODOs)
interface HttpClient {
    public function get(string $url): string;
}

class BaseHttpClient implements HttpClient {
    public function get(string $url): string {
        // Live to real endpoint
        return file_get_contents($url);
    }
}

abstract class HttpDecorator implements HttpClient {
    public function __construct(protected HttpClient $wrapped) {}
}

class LoggingDecorator extends HttpDecorator {
    public function get(string $url): string {
        echo "[Log] GET $url\n";
        $r = $this->wrapped->get($url);
        echo "[Log] Got " . strlen($r) . " bytes\n";
        return $r;
    }
}

class CachingDecorator extends HttpDecorator {
    private array $cache = [];
    public function get(string $url): string {
        if (isset($this->cache[$url])) {
            echo "[Cache] Hit $url\n";
            return $this->cache[$url];
        }
        $r = $this->wrapped->get($url);
        $this->cache[$url] = $r;
        echo "[Cache] Stored $url\n";
        return $r;
    }
}

// Missing 1: Implement RetryDecorator (3 tries, catch Exception)
class RetryDecorator extends HttpDecorator {
    public function get(string $url): string {
        for ($i = 0; $i < 3; $i++) {
            try {
                return $this->wrapped->get($url);
            } catch (Exception $e) {
                if ($i == 2) throw $e;
            }
        }
        return "[TODO] Retry not implemented";
    }
}

// Missing 2: Implement TokenCounterDecorator
class TokenCounterDecorator extends HttpDecorator {
    public function get(string $url): string {
        $r = $this->wrapped->get($url);
        echo "[Tokens] " . str_word_count($r) . "\n";
        return $r;
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "WITH Decorator (GOOD - complete TODOs):\n";
    $url = "https://jsonplaceholder.typicode.com/posts/1";

    // Demo: Full pipeline with all 4 decorators stacked at runtime
    echo "--- Full Pipeline (TokenCounter -> Retry -> Caching -> Logging -> Base) ---\n";
    $fullPipeline = new TokenCounterDecorator(
        new RetryDecorator(
            new CachingDecorator(
                new LoggingDecorator(
                    new BaseHttpClient()
                )
            )
        )
    );
    echo "Call 1 (live fetch):\n";
    echo substr($fullPipeline->get($url), 0, 60) . "...\n";
    echo "Call 2 (cached fetch):\n";
    echo substr($fullPipeline->get($url), 0, 60) . "...\n";

    // Missing 3: Reorder decorators: new Logging(new Caching(...)) vs new Caching(new Logging(...))
    echo "\n--- Order Matters Demo ---\n";
    echo "Case A: Logging(Caching(Base)) - Logger is outer:\n";
    $orderA = new LoggingDecorator(new CachingDecorator(new BaseHttpClient()));
    $orderA->get($url); // Miss: Logs and Stores
    $orderA->get($url); // Hit: Still logs [Log] GET because Logger is outside Cache!

    echo "\nCase B: Caching(Logging(Base)) - Cache is outer:\n";
    $orderB = new CachingDecorator(new LoggingDecorator(new BaseHttpClient()));
    $orderB->get($url); // Miss: Logs and Stores
    $orderB->get($url); // Hit: Only [Cache] Hit - does NOT reach Logger!

    echo "\n4 decorators = 16 combinations with 5 classes (Base + 4 Decorators).\n";
}
