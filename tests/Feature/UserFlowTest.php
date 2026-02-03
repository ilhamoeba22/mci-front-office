<?php

namespace Tests\Feature;

use App\Models\Queue;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Transfer;
use App\Services\CoreBankingService;
use Tests\TestCase;
use Mockery;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserFlowTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock Core Banking API
        $this->mock(CoreBankingService::class, function ($mock) {
            $mock->shouldReceive('getBalance')
                 ->with('1234567890')
                 ->andReturn([
                     'accountNo' => '1234567890',
                     'name' => 'Budi Santoso',
                     'accountInfo' => [
                         ['availableBalance' => ['value' => 5000000], 'minimalAmount' => ['value' => 50000]]
                     ],
                     'mobileNo' => '081234567890',
                     'identityNo' => '3201234567890001',
                     'address' => 'Jl. Merdeka No. 1'
                 ]);
        });
    }

    /** @test */
    public function user_can_create_cs_queue()
    {
        $response = $this->post(route('queue.store'), [
            'jenis' => 'CS'
        ]);

        $response->assertStatus(302); // Redirect to ticket
        $this->assertDatabaseHas('tbl_antrian', [
            'type' => 'CS',
            'st_antrian' => '0'
        ]);
        
        // Get the latest queue to verify token in session/redirect
        $queue = Queue::latest('created')->first();
        $response->assertRedirect(route('queue.show', $queue->kode));
    }

    /** @test */
    public function user_can_create_teller_queue()
    {
        $response = $this->post(route('queue.store'), [
            'jenis' => 'Teller'
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('tbl_antrian', [
            'type' => 'Teller',
            'st_antrian' => '0'
        ]);
    }

    /** @test */
    public function user_can_perform_deposit()
    {
        // 1. Check Account
        $response = $this->post(route('transaction.check'), [
            'no_rek' => '1234567890',
            'type' => 'deposit'
        ]);
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso'); // Nama pemilik

        // 2. Store Deposit
        $response = $this->post(route('transaction.deposit.store'), [
            'nama' => 'Budi Santoso',
            'no_rek' => '1234567890',
            'nominal' => 100000,
            'terbilang' => 'Seratus Ribu Rupiah',
            'nama_penyetor' => 'Andi',
            'hp_penyetor' => '08987654321',
            'tujuan' => 'Tabungan'
        ]);

        $response->assertStatus(302);
        
        // 3. Verify Database
        $this->assertDatabaseHas('tbl_setor', [
            'no_rek' => '1234567890',
            'nominal' => 100000,
            'nama_penyetor' => 'Andi'
        ]);

        // 4. Verify Teller Queue created
        $this->assertDatabaseHas('tbl_antrian', [
            'nama_antrian' => 'Budi Santoso',
            'type' => 'Teller'
        ]);
    }

    /** @test */
    public function user_can_perform_withdrawal()
    {
        // 1. Check Account
        $response = $this->post(route('transaction.check'), [
            'no_rek' => '1234567890',
            'type' => 'withdrawal'
        ]);
        $response->assertStatus(200);

        // 2. Store Withdrawal
        $response = $this->post(route('transaction.withdrawal.store'), [
            'nama' => 'Budi Santoso',
            'no_rek' => '1234567890',
            'nominal' => 200000,
            'nama_penarik' => 'Budi Santoso',
            'hp_penarik' => '081234567890',
            'tujuan' => 'Ambil Tunai'
        ]);

        $response->assertStatus(302);

        // 3. Verify DB
        $this->assertDatabaseHas('tbl_tarik', [
            'no_rek' => '1234567890',
            'nominal' => 200000
        ]);
    }

    /** @test */
    public function user_can_perform_transfer()
    {
        // 1. Check Account
        $response = $this->post(route('transaction.check'), [
            'no_rek' => '1234567890',
            'type' => 'transfer'
        ]);
        $response->assertStatus(200);

        // 2. Store Transfer
        $response = $this->post(route('transaction.transfer.store'), [
            'nama' => 'Budi Santoso',
            'no_rek' => '1234567890',
            'nominal' => 50000,
            'nama_penyetor' => 'Budi Santoso',
            'hp_penyetor' => '081234567890',
            'alamat_penyetor' => 'Jl. Test',
            
            'bank_tujuan' => 'BCA/6500',
            'no_rek_tujuan' => '987654321',
            'nama_tujuan' => 'Siti Aminah',
            'berita_tujuan' => 'Bayar Hutang',
            
            'tujuan' => 'Transfer',
        ]);

        $response->assertStatus(302);

        // 3. Verify DB
        $this->assertDatabaseHas('tbl_transfer', [
            'no_rek' => '1234567890',
            'bank_tujuan' => 'BCA',
            'biaya_trf' => 6500,
            'nominal' => 50000
        ]);
    }
}
