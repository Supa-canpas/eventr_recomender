import requests
from bs4 import BeautifulSoup
import time

class EventInfo:
    # self.infoUrlCafe
    # self.infoUrlPopUp
    # self.infoUrlExhib
    # self.event_details
    # self.base_url 
    
    def __init__(self, base_url):
        self.base_url = base_url
        self.infoUrlCafe = []
        self.infoUrlPopUp = []
        self.infoUrlExhib = []

    def getEventUrl(self):
        # url = 'https://collabo-cafe.com/'
        res = requests.get(self.base_url)
        soup = BeautifulSoup(res.text, 'html.parser')
        eventListdiv = soup.find_all('div','widget__post__list')[:3]
        for i in range(3):
            event_links = eventListdiv[i].find_all('a')
            for link in event_links:
                buf = link["href"]
                if i == 0:
                    self.infoUrlCafe.append(buf)
                elif i == 1:
                    self.infoUrlPopUp.append(buf)
                elif i == 2:
                    self.infoUrlExhib.append(buf)

    def getEventInfo(self, event_url):
        # url = 'https://collabo-cafe.com/events/collabo/shingeki-gigo-cafe-ikebukuro2024/'
        res = requests.get(event_url)
        soup = BeautifulSoup(res.text, 'html.parser')
        title = soup.find('h1').text
        details_table = soup.select_one('#main > article > section.entry-content > div.table__container > table')
        try:
            records = details_table.find_all('tr')
            self.event_details = []
            self.event_details.append(title)
            for i in range(0,4):
                if i == 0 or i == 3:
                    try:
                        buf = records[i].find('a')['href']
                    except:
                        buf = 'empty'
                else:
                    buf = records[i].find('td').text
                self.event_details.append(buf)
        except:
            pass

    def printEventInfo(self, infoUrl):
        for event_url in infoUrl:  
            self.getEventInfo(event_url)
            for info in self.event_details:
                print(info)
                print("separate_table")
            print("separate_site")
            time.sleep(1)

if __name__ == '__main__':
    url = 'https://collabo-cafe.com/'
    iventinfo = EventInfo(url)
    iventinfo.getEventUrl()
    print("コラボカフェ")
    iventinfo.printEventInfo(iventinfo.infoUrlCafe)
    print("ポップアップストア")
    iventinfo.printEventInfo(iventinfo.infoUrlPopUp)
    print("原画展・展示会")
    iventinfo.printEventInfo(iventinfo.infoUrlExhib)
    