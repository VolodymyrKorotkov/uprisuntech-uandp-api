<?php declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class FormatPublicKeyRS256StringCommand extends Command
{
    const BEGIN_PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----";
    const END_PUBLIC_KEY = "-----END PUBLIC KEY-----";

    public function __construct(
        #[Autowire(param: 'app.jwt_public_key')] private readonly string $jwtPublicKeyPath
    )
    {
        parent::__construct('app:format_public_key:RS256_string');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $formattedKey = $this->formatPublicKey(
            file_get_contents($this->jwtPublicKeyPath)
        );

        file_put_contents($this->jwtPublicKeyPath, $formattedKey);

        $style = new SymfonyStyle($input, $output);
        $style->info($formattedKey);
        $style->success('Success!!');

        return 0;
    }

    private function formatPublicKey(string $keyString): string
    {
        $matches = [];
        preg_match_all('/.{1,64}/', $keyString, $matches);

        return self::BEGIN_PUBLIC_KEY . PHP_EOL . implode(PHP_EOL, $matches[0]) . PHP_EOL . self::END_PUBLIC_KEY;
    }
}
