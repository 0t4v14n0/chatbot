from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
import os
import time
import requests

#se nao estiver criada, criarar uma pasta para o armazenamento das informacoes do zap
#para que nao fique colocando o QR code direto
dir_path = os.getcwd()
chrome_options2 = Options()
chrome_options2.add_argument(r"user-data-dir=" + dir_path + "/pasta/sessao")
driver = webdriver.Chrome(options=chrome_options2)
driver.get('https://web.whatsapp.com/')
time.sleep(60)

def bot():
    #enquanto nao tiver mensagem com bolinha verde irar cair no except
    try:
        #pega o fical da classe da bolinha verde
        bolinha = driver.find_element(By.CLASS_NAME,'aumms1qt')
        bolinha = driver.find_elements(By.CLASS_NAME,'aumms1qt')
        clica_bolinha = bolinha[-1]
        acao_bolinha = webdriver.common.action_chains.ActionChains(driver)
        #posicao do clique levando relaco da bolinha verde pra esqueda 
        acao_bolinha.move_to_element_with_offset(clica_bolinha,0,-20)
        acao_bolinha.click()
        acao_bolinha.perform()
        acao_bolinha.click()
        acao_bolinha.perform()

    except:
        print("ola")

while True:
    bot()
