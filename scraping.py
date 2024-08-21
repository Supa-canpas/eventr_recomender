import requests
from bs4 import BeautifulSoup

class EventInfo:
    # self.infoUrl
    # self.info
    # self.ccurl 
    
    def __init__(self,base_url):
        self.base_url = base_url
        self.event_urls = []
        self.event_details = []

    def getEventUrl(self):
        # url = 'https://collabo-cafe.com/'
        res = requests.get(self.base_url)
        soup = BeautifulSoup(res.text, 'html.parser')
        eventListdiv = soup.find('div','widget__post__list')
        event_links = eventListdiv.find_all('a')
        for link in event_links:
            self.event_urls.append(link['href'])

    def getEventInfo(self,event_url):
        # url = 'https://collabo-cafe.com/events/collabo/shingeki-gigo-cafe-ikebukuro2024/'
        res = requests.get(event_url)
        soup = BeautifulSoup(res.text, 'html.parser')
        title = soup.find('h1').text
        details_table = soup.select_one('#main > article > section.entry-content > div.table__container > table')
        records = details_table.find_all('tr')
        self.event_details.append(title)
        for i in range(1,3):
            if i == 3:
                buf = records[i].find('a')['href']
                self.event_details.append(buf)
            else:
                buf = records[i].find('td').text
                self.event_details.append(buf)

if __name__ == '__main__':
    # url = "https://collabo-cafe.com/events/collabo/shingeki-gigo-cafe-ikebukuro2024/"
    # info = getInfo(url)
    # print(info)

    event_info = EventInfo('https://collabo-cafe.com/')
    event_info.getEventUrl()
    for event_url in event_info.event_urls:  
        event_info.getEventInfo(event_url)
        print(event_info.event_details)