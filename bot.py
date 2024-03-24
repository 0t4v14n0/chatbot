from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
import os
import time

#facilitar munutencao
bolinha_not = ('lyutrhe2')
contato_cli = ('//*[@id="main"]/header/div[2]/div/div/div/span')
msg_cliente = ('_21Ahp')
caixa_msg   = ('//*[@id="main"]/footer/div[1]/div/span[2]/div/div[2]/div[1]/div/div[1]/p')
caixa_msg2 = ('div[title="Digite uma mensagem"]')
caixa_pesquisa = ('div[title="Caixa de texto de pesquisa"]')

#se nao estiver criada, criarar uma pasta para o armazenamento das informacoes do zap
#para que nao fique colocando o QR code direto
dir_path = os.getcwd()
chrome_options2 = Options()
chrome_options2.add_argument(r"user-data-dir=" + dir_path + "/pasta/sessao")
driver = webdriver.Chrome(options=chrome_options2)
driver.get('https://web.whatsapp.com/')
time.sleep(30)

def bot():
    #PEGAR BOLINHA
    #enquanto nao tiver mensagem com bolinha verde irar cair no except
    try:
        #pega o final da classe da bolinha verde
        bolinha = driver.find_element(By.CLASS_NAME,bolinha_not)
        bolinha = driver.find_elements(By.CLASS_NAME,bolinha_not)
        clica_bolinha = bolinha[-1]
        acao_bolinha = webdriver.common.action_chains.ActionChains(driver)
        acao_bolinha.move_to_element_with_offset(clica_bolinha,0,-20)
        acao_bolinha.click()
        acao_bolinha.perform()
        acao_bolinha.click()
        acao_bolinha.perform()
        time.sleep(5)

        #PEGAR NUMERO
        numero_cliente = driver.find_element(By.XPATH,contato_cli)
        telefone = numero_cliente.text
        print('telefone e: ',telefone)
        time.sleep(5)

        #pegar msg
        todas_msg = driver.find_elements(By.CLASS_NAME,msg_cliente)
        todas_msg_texto = [e.text for e in todas_msg]
        msg = todas_msg_texto[-1]
        print('ultima mensagem: ',msg)
        time.sleep(5)

        #RESPONDENDO CLIENTE
        campo_de_texto = driver.find_element(By.XPATH,caixa_msg)
        campo_de_texto.click()
        time.sleep(1)
        campo_de_texto.send_keys('Olá aqui é o Bot', Keys.ENTER)

    except Exception as e:
        print("ERRO: ")

while True:
    bot()
