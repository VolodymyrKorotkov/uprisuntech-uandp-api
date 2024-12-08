<?php declare(strict_types=1);

namespace App\Service\GovIdDecryptor\Dto;

use App\ArgumentResolver\FromRequestDtoInterface;

final class GovUaSenderInfo implements FromRequestDtoInterface
{
	/**
	 * @var string Время подписи в формате MM.DD.YYYY HH:ii:ss
	 */
	public string $signTime = '';

	/**
	 * @var bool Используется ли TSP
	 */
	public bool $useTSP = false;


	//Owner Info
	/**
	 * @var string Реквизиты представителя сертификата
	 */
	public string $issuer = '';

	/**
	 * @var string Общее имя представителя (сертификата)
	 */
	public string $issuerCN = '';

	/**
	 * @var string Регистрационный номер сертификата представителя
	 */
	public string $serial = '';

	/**
	 * @var string Реквизиты пользователя сертификата
	 */
	public string $subject = '';

	/**
	 * @var string Общее имя пользователя
	 */
	public string $subjCN = '';

	/**
	 * @var string Название организации где работает
	 */
	public string $subjOrg = '';

	/**
	 * @var string Подразделение
	 */
	public string $subjOrgUnit = '';

	/**
	 * @var string Должность
	 */
	public string $subjTitle = '';

	/**
	 * @var string Область
	 */
	public string $subjState = '';

	/**
	 * @var string Город или пгт, где живет пользователь
	 */
	public string $subjLocality = '';

	/**
	 * @var string Полное имя
	 */
	public string $subjFullName = '';

	/**
	 * @var string Адрес проживания
	 */
	public string $subjAddress = '';

	/**
	 * @var string Телефон
	 */
	public string $subjPhone = '';

	/**
	 * @var string Email
	 */
	public string $subjEMail = '';

	/**
	 * @var string DNS
	 */
	public string $subjDNS = '';

	/**
	 * @var string Код ЄДРПОУ пользователя
	 */
	public string $subjEDRPOUCode = '';

	/**
	 * @var string ДРФО код
	 */
	public string $subjDRFOCode = '';
}
