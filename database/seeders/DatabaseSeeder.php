<?php

namespace Database\Seeders;

use App\Models\EvaluationTemplate;
use App\Models\EvaluationTemplateItem;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $organization = Organization::create([
            'name' => 'EVOLUA Demo',
            'slug' => Str::slug('evolua-demo'),
            'primary_color' => '#16a34a',
        ]);

        User::create([
            'organization_id' => $organization->id,
            'name' => 'Admin EVOLUA',
            'email' => 'admin@evolua.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_OWNER,
        ]);

        Subscription::create([
            'organization_id' => $organization->id,
            'plan' => 'starter',
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
            'current_period_ends_at' => now()->addDays(14),
            'max_active_athletes' => 20,
        ]);

        $this->seedTemplate(
            name: 'Futebol Base - Geral',
            sport: 'Futebol',
            isDefault: true,
            domains: [
                'tecnico' => [
                    'Passe curto',
                    'Passe longo',
                    'Dominio/primeiro toque',
                    'Conducao',
                    'Finalizacao',
                    'Drible 1x1',
                ],
                'fisico' => [
                    'Velocidade',
                    'Agilidade',
                    'Resistencia',
                    'Forca',
                    'Impulsao',
                ],
                'tatico' => [
                    'Posicionamento',
                    'Tomada de decisao',
                    'Leitura de jogo',
                    'Marcacao/pressao',
                    'Cobertura',
                    'Transicao',
                ],
                'mental' => [
                    'Disciplina',
                    'Concentracao',
                    'Competitividade',
                    'Trabalho em equipe',
                    'Resiliencia',
                ],
            ],
        );

        $this->seedTemplate(
            name: 'Futebol - Goleiro',
            sport: 'Futebol',
            isDefault: false,
            domains: [
                'tecnico' => [
                    'Reflexo',
                    'Saida do gol',
                    'Reposicao com mao',
                    'Reposicao com pe',
                    'Encaixe',
                    'Queda lateral',
                ],
                'fisico' => [
                    'Agilidade',
                    'Impulsao',
                    'Forca',
                    'Resistencia',
                ],
                'tatico' => [
                    'Posicionamento',
                    'Comando de area',
                    'Leitura de cruzamentos',
                ],
                'mental' => [
                    'Concentracao',
                    'Coragem',
                    'Lideranca',
                ],
            ],
        );
    }

    protected function seedTemplate(string $name, string $sport, bool $isDefault, array $domains): void
    {
        $template = EvaluationTemplate::create([
            'organization_id' => null,
            'sport' => $sport,
            'name' => $name,
            'scale_min' => 1,
            'scale_max' => 10,
            'is_default' => $isDefault,
        ]);

        $sortOrder = 1;

        foreach ($domains as $domain => $items) {
            foreach ($items as $label) {
                EvaluationTemplateItem::create([
                    'template_id' => $template->id,
                    'domain' => $domain,
                    'label' => $label,
                    'weight' => 1,
                    'sort_order' => $sortOrder,
                ]);

                $sortOrder++;
            }
        }
    }
}
