<?php

namespace Altitude;

class Cleanup
{
    /**
     * Every file Altitude 0.x published, with the sha256 of its LF bytes and of
     * the same bytes checked out with CRLF line endings (git autocrlf).
     *
     * @var array<string, array{string, string}>
     */
    public const HASHES = [
        '.ai/guidelines/tall-stack.md' => [
            '912e23dad67c809b4f5f288660b51deb53f9fc00ec1014ae462720bf2253a8e5',
            'a321fb4ccba8e344c5c880c7994a52b7fff8ce09e38429bab0d09b8ab873b574',
        ],
        '.claude/agents/alpine.md' => [
            'ebc4266424f0ba751a1b5a149cdecb46dab5fd2fa3a4992ada378a802db11bc1',
            '7c3d70d5fbfa4f907d3ec44a4e21cb6776d668af483be8c129e7574bd3dbe26b',
        ],
        '.claude/agents/architect.md' => [
            '538c7bdebe64822bf4a381084f5672ad749664ed44eb29350b7b01cdbae3ad43',
            'b7ebe7850000895642bb99026e2d0844b1a07794c17b750960319626ea14cf90',
        ],
        '.claude/agents/database.md' => [
            'f46543811fc648a4edaba73c35b71cf16ff2fb11cdec0cdbd51a8c2abfa0525f',
            '1249f4ec8b7b5f2445f9f09b3d16d436307007de864d67b8dc95c4ac56d30e3d',
        ],
        '.claude/agents/docs.md' => [
            '0297e906eef0a7d7279cfa78be98a7196269e6188e4b11928ab7327fa5abd92d',
            'a749a6b1c8ab6900ad2753da6fd6ae6de01b1632df5100703b274f5d75432f17',
        ],
        '.claude/agents/filament.md' => [
            'fbccdab947157dd15239949e26bae4ccef6f2cc2bce7ac2a04c5446cbeea58f1',
            '3faf9bb65019a67736c9e81e621178f57b95020698cb6f80fcf9c94f122b9a93',
        ],
        '.claude/agents/flux.md' => [
            '09505e9a334f26cff3fa2e04c3d33983429d31bc3db1511063d3c54d32aa41e8',
            'fe224c7a802f56ce2ffffdddc0888c2ff35778a70732bc75e4bef1b7b8e24d8f',
        ],
        '.claude/agents/livewire.md' => [
            '010e43072f58a73139d5d29fd507ed5b52b0e09db9ded0d56f99036bdfca12c0',
            '03d65610030ae9742e1b616a30ecc2ca3c0ea7e2b782d6a25398ecd74a56edad',
        ],
        '.claude/agents/pest.md' => [
            '5014ad5e0b3c0ed91f9fc4b02098d28cc396b5ef504c646ee1a8788af7cb61a7',
            '37f6ad2943d2384216a8a93abd03238c24fb74407729d90970bea86f85f0bd0c',
        ],
        '.claude/agents/realtime.md' => [
            '8118af0362c402a7ce76a18f42b0839e8dc21f8432d1f51b75a6d21957b2a340',
            'a7319b32522fa9ee98cdc3c9256674ae036ebf8694e5aff933d676eb6cc56259',
        ],
        '.claude/agents/security.md' => [
            'a5285890bdf4429e0e51a25521ce67ba2b90373a40bb1ab3e612e53dfd07c549',
            'a2ab0fcf80e94db3a855a5018141a0a0799c2dc961811698354f66755c0495e9',
        ],
        '.claude/commands/catchup.md' => [
            'a0a9347a85c6dad7f6a71b3d4ec2439bd82da89ec05026eda44d57f1803f71bc',
            'e8d880f5c304b01219329623b97808c1fa116e13cfe1016ff65d874b75e3d2f1',
        ],
        '.claude/commands/debug.md' => [
            '73f08e34e390d4f3f607b51cca9cbb60e2533ca29c1e0a7772073e3357666a15',
            'caba2e0172b1028ce915d11e55e1fc4c8693219ccd57f865aced6406627505a1',
        ],
        '.claude/commands/pint.md' => [
            '7f483fa3b3ef6a6c987d6a73822ccd74bfc1c3c06be895e8d0766ac8ef1ed350',
            '4b2b7ca909bbca9d3c3ab25737b8d1b8401f408939da919897e45a4cb95a9560',
        ],
        '.claude/commands/review.md' => [
            '14890a7d42e889a94b2302006cd7c6f4a608ccb2ee42f5a87c694f9744d3a0ed',
            '4c7fca8f9a8a06dedbf3bf5aee1e89d2b4c9680eb388edb3510a13ed31d21330',
        ],
        '.claude/commands/ship.md' => [
            '84d1452e22d2ca996dd4dd288841ccc39f63450176e172d4f8f5255e9d38f9df',
            'eae634b09c928529aba4be0390afa9e409e9d35b0e3477b7815c9b07380cdc70',
        ],
        '.claude/commands/test.md' => [
            'a86c6de4032a77999159e330da42d6df28b34fc46cac456832ed2c04553f517b',
            '238ef1eb1ddf51beda5aea62594c39b6f982daba6c0f82ae1d00c455ed952e14',
        ],
        'config/altitude.php' => [
            'c6f2d9134f707a1e93ec72cb2b0775cf8bba6253df91ce37c1c67fda9ea28252',
            '0b8947889c8bcaaa7c8d7ff9bdcd1b722a8ebe5f4965f1a2cb86b0b003b69c92',
        ],
    ];

    public const DIRECTORIES = [
        '.claude/agents',
        '.claude/commands',
    ];

    /**
     * @return array{deleted: list<string>, edited: list<string>}
     */
    public function run(string $basePath): array
    {
        $deleted = [];
        $edited = [];

        foreach (self::HASHES as $path => $hashes) {
            $file = $basePath.'/'.$path;

            if (! is_file($file)) {
                continue;
            }

            if (in_array(hash_file('sha256', $file), $hashes, true)) {
                unlink($file);
                $deleted[] = $path;
            } else {
                $edited[] = $path;
            }
        }

        foreach (self::DIRECTORIES as $path) {
            $directory = $basePath.'/'.$path;

            if (is_dir($directory) && scandir($directory) === ['.', '..']) {
                rmdir($directory);
                $deleted[] = $path.'/';
            }
        }

        return ['deleted' => $deleted, 'edited' => $edited];
    }
}
